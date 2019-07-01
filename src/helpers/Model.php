<?php
/*
|--------------------------------------------------------------------------
| Fibers model helpers
|--------------------------------------------------------------------------
|
| Assorted model helpers to get additional information about a specific
| model, like namespace, attributes, relationship etc.
|
*/

namespace Fibers\Helper\Helpers;

use Fibers\Helper\Traits\Collect;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use ReflectionClass;

class Model
{
    use Collect, Macroable;

    private $path;
    private $namespace;
    private $name;
    private $class;
    private $instance;
    private $table;
    private $primary;
    private $hidden;
    private $fillable;

    // --------- CONSTRUCTORS ------------ //

    /**
     * Load a model from file
     * @param string $path
     * @return Model
     */
    public function fromFile(string $path): Model
    {
        // grab the contents of the file
        $contents = file_get_contents($path);

        // start with a blank namespace and class
        $namespace = $class = "";

        // set helper values to know that we have found the namespace/class token and need to collect the string values after them
        $getting_namespace = $getting_class = false;

        // go through each token and evaluate it as necessary
        foreach (token_get_all($contents) as $token) {

            //If this token is the namespace declaring, then flag that the next tokens will be the namespace name
            if (is_array($token) && $token[0] == T_NAMESPACE) {
                $getting_namespace = true;
            }

            //If this token is the class declaring, then flag that the next tokens will be the class name
            if (is_array($token) && $token[0] == T_CLASS) {
                $getting_class = true;
            }

            //While we're grabbing the namespace name...
            if ($getting_namespace === true) {

                //If the token is a string or the namespace separator...
                if(is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {

                    //Append the token's value to the name of the namespace
                    $namespace .= $token[1];

                }
                else if ($token === ';') {

                    //If the token is the semicolon, then we're done with the namespace declaration
                    $getting_namespace = false;

                }
            }

            //While we're grabbing the class name...
            if ($getting_class === true) {

                //If the token is a string, it's the name of the class
                if(is_array($token) && $token[0] == T_STRING) {

                    //Store the token's value as the class name
                    $class = $token[1];

                    //Got what we need, stope here
                    break;
                }
            }
        }

        // build the fully-qualified class name and save it
        $this->namespace = $namespace;
        $this->name = $class;
        $this->class = $namespace ? $namespace . '\\' . $class : $class;
        $this->path = $path;

        // return this instance
        return $this;
    }

    /**
     * Load a model from class
     * @param string $class
     * @return Model
     */
    public function fromClass(string $class): Model
    {
        $this->class = $class;
        $this->name = class_basename($class);
        $this->namespace = ltrim("\\", Str::before($this->class, $this->name));
        return $this;
    }

    // --------- GETTERS ------------ //

    /**
     * Get class reflection
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    public function reflection (): ReflectionClass
    {
        return new ReflectionClass($this->class);
    }

    /**
     * Immediate parent of a model class
     * Note that this might not be useful as user can extend from
     * custom 'model' class not Laravel one.
     * @return string
     */
    public function parent (): string
    {
        return get_parent_class($this->class);
    }

    /**
     * Model's class (namespace + name)
     * @return string
     */
    public function class (): string
    {
        return $this->class;
    }

    /**
     * Model's name without namespace
     * @return string
     */
    public function name (): string
    {
        return $this->name;
    }

    /**
     * Models's namespace
     * @return string
     */
    public function namespace (): string
    {
        return $this->namespace;
    }

    /**
     * Initiate a new instance of a model
     * @return mixed
     */
    public function instance ()
    {
        if ($this->instance) return $this->instance;
        return $this->instance = new $this->class();
    }

    /**
     * Get primary key for a model
     * @return string
     */
    public function primary (): string
    {
        if ($this->primary) return $this->primary;
        return $this->primary = $this->instance()->getKeyName();
    }

    /**
     * Get model's table
     * @return string
     */
    public function table (): string
    {
        if ($this->table) return $this->table;
        return $this->table = $this->instance()->getTable();
    }

    /**
     * Get hidden attributes
     * @return Collection
     */
    public function hidden (): Collection
    {
        if ($this->hidden) return $this->hidden;
        return $this->hidden = collect($this->instance()->getHidden());
    }

    /**
     * Get fillable attributes
     * @return Collection
     */
    public function fillable (): Collection
    {
        if ($this->fillable) return $this->fillable;
        return $this->fillable = collect($this->instance()->getFillable());
    }

    /**
     * Get model's attributes
     * @param bool $deepAnalysis
     * @return Collection
     */
    public function attributes (bool $deepAnalysis = false): Collection
    {
        // TODO: do deep analysis, comparing with FormRequest, Controller, Model, ...
        // TODO: save this to cache
        $hidden = $this->hidden()->toArray();
        try {
            return collect(DB::select('show columns from ' . $this->table()))->mapWithKeys(function ($item) use ($hidden) {
                preg_match("/^(\w+)(?:\((.*)\))?(?:\s*(\w+))?$/", $item->Type, $match);
                return [$item->Field => (Object)[
                    "name" => $item->Field,
                    "type" => $match[1],
                    "arguments" => collect(isset($match[2]) ? explode(',', $match[2]) : [])->map(function ($i) {
                        return ltrim(rtrim(ltrim(rtrim(trim($i), "'"), "'"), "\""), "\"");
                    }),
                    "unsigned" => isset($match[3]),
                    "key" => (['PRI' => 'primary', 'UNI' => 'unique', 'IND' => 'index'][$item->Key]) ?? null,
                    "required" => ($item->Null === 'NO' && $item->Default === null),
                    "default" => $item->Default,
                    "hidden" => in_array($item->Field, $hidden),
                ]];
            });
        } catch (QueryException $exception) {
            return collect();
        }
    }

    /**
     * Build validation rules
     * @param bool $fillableOnly = true
     * @return Collection
     */
    public function rules ($fillableOnly = true): Collection
    {
        // TODO: try to get rules from FormRequest if exist
        return $this
            ->attributes()
            ->when($fillableOnly, function ($collection) {
                return $collection
                ->filter(function ($item) {
                    return $this->fillable()->contains($item->name);
                });
            })
            ->filter(function ($item) {
                return !$item->hidden && ($item->required or $this->attributeValidationType($item->type));
            })
            ->mapWithKeys(function ($item) {
                $rules = collect();
                if ($item->required) $rules->push("required");
                if ($type = $this->attributeValidationType($item->type)) $rules->push($type);
                if ($type === "string" and $item->arguments->count()) $rules->push("max:{$item->arguments->get(0)}");
                if ($item->key === "unique") $rules->push("unique:{$this->table()}");
                return [$item->name => $rules->join("|")];
            });
    }

    /**
     * Get model's relationships
     * @return Collection
     */
    public function relationships (): Collection
    {
        $relationships = collect();
        // get methods
        $methods = array_diff(get_class_methods($this->class),get_class_methods(get_parent_class($this->class)));
        foreach ($methods as $method) {
            // stop when we get to __construct
            if ($method === "__construct") break;
            // check if method returns a Relationship
            try {
                $result = $this->instance()->{$method}();
                if ($result and is_object($result) and preg_match("/^(?:.*)Relations\\\(.*)$/", get_class($result), $matches) and count($matches) > 1) {
                    $variant = Str::kebab(str_replace("To", "", $matches[1]));
                    $related = get_class($result->getRelated());
                    if (!in_array($related, ["Illuminate\\Notifications\\DatabaseNotification"])) {
                        $relationships->put($method, (object) [
                            "name"=> $method,
                            "type" => "relationship",
                            "variant" => $variant === "belongs" ? "belongs-one" : $variant,
                            "related" => $related,
                            "parent" => get_class($result->getParent()),
                            "required" => false,
                            "pivot" => $variant === "belongs-many" ? $result->getPivotColumns() : [],
                            "table" => $variant === "belongs-many" ? $result->getPivotTable() : null
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // catch all exceptions
            } catch (\Throwable $e) { // For PHP 7
                // handle $e
            }
        }
        // return
        return $relationships;
    }

    /**
     * Get attribute and relationship fields (either native HTML or Vue extended)
     * This method basically maps attribute type to corresponding field/input type.
     * @param bool $fillable = false
     * @param bool $extended = false
     * @return Collection
     */
    public function fields ($fillable = false, $extended = false): Collection
    {
        return $extended ? $this->fieldsExtended($fillable) : $this->fieldsNative($fillable);
    }

    // --------- HELPERS ------------ //

    /**
     * Get HTML native fields
     * @param bool $fillable
     * @param bool $withInfo
     * @return Collection
     */
    private function fieldsNative ($fillable = false, $withInfo = true): Collection
    {
        // collect attribute fields
        $attributes = $this->attributes()->when($fillable, function ($collection) {
            $fillable = $this->fillable();
            return $collection->filter(function ($item, $key) use ($fillable) {
                return $fillable->contains($key);
            });
        })->map(function ($item) {
            $field = $this->attributeFieldType($item->name, $item->type);
            if (!$field) return null;
            return (Object) [
                "name" => $item->name,
                "type" => $field->field,
                "args" => $field->type,
                "required" => $item->required,
                "default" => $item->default,
                "hidden" => $item->hidden,
                "options" => $item->arguments
            ];
        })->values()->filter();

        // collect relationship fields
        $relationships = $this->relationships()->map(function ($item) {
            if (!in_array($item->variant, ["belongs-many", "belongs-one"])) return null;
            return (Object) [
                "name" => $item->name,
                "type" => "relationship",
                "args" => $item->variant === "belongs-many" ? "multiple" : null,
                "required" => false,
                "default" => null,
                "hidden" => false,
                "related" => $item->related
            ];
        })->values()->filter();

        // combine and return
        return $attributes->concat($relationships)->when($withInfo, function ($collection) {
            return $collection->map(function ($field) {
                $field->info = (Object) [
                    "id" => Str::slug("input-{$field->name}"),
                    "title" => Str::title($field->name),
                    "name" => $field->name,
                    "required" => $field->required ? " required" : "",
                    "placeholder" => $field->default ? " placeholder=\"{$field->default}\"" : "",
                    "hidden" => $field->hidden ? " hidden" : "",
                    "end" => ($field->required ? " required" : "").($field->default ? " placeholder=\"{$field->default}\"" : "").($field->hidden ? " hidden" : "")
                ];
                return $field;
            });
        })->mapWithKeys(function ($item) {
            return [$item->name => $item];
        });
    }

    /**
     * Get extended Vue fields
     * @param bool $fillable
     * @return Collection
     */
    private function fieldsExtended($fillable = false): Collection
    {
        return collect();
    }

    /**
     * Helper method; maps attribute type to validation type
     * @param $key
     * @return string|null
     */
    private function attributeValidationType($key)
    {
        if (in_array($key, ["decimal", "double", "float", "integer", "mediumInteger", "smallInteger", "tinyInteger"])) {
            return "numeric";
        } elseif (in_array($key, ["char", "lineString", "longText", "mediumText", "multiLineString", "string", "text", "uuid"])) {
            return "string";
        } elseif (in_array($key, ["date", "dateTime", "dateTimeTz", "time", "timeTz", "timestamp", "timestampTz", "year"])) {
            return "date";
        } else {
            return null;
        }
    }

    /**
     * Helper method; maps attribute type to field type
     * @param $name
     * @param $type
     * @return object|null
     */
    private function attributeFieldType($name, $type)
    {
        if (in_array($name, ["password", "pass"])) {
            return (Object) ["field" => "input", "type" => "password"];
        }
        elseif ($name === "month") {
            return (Object) ["field" => "input", "type" => "month"];
        }
        elseif (in_array($name, ["color", "colour"])) {
            return (Object) ["field" => "input", "type" => "color"];
        }
        elseif (in_array($name, ["email", "mail"])) {
            return (Object) ["field" => "input", "type" => "email"];
        }
        elseif (in_array($name, ["file", "image"])) {
            return (Object) ["field" => "input", "type" => "file"];
        }
        elseif (in_array($name, ["tel", "telephone", "phone", "mobile"])) {
            return (Object) ["field" => "input", "type" => "tel"];
        }
        elseif (in_array($name, ["url", "link", "page"])) {
            return (Object) ["field" => "input", "type" => "url"];
        }
        elseif (in_array($type, ["decimal", "double", "float", "integer", "mediumInteger", "smallInteger"])) {
            return (Object) ["field" => "input", "type" => "number"];
        }
        elseif (in_array($type, ["tinyInteger", "boolean"])) {
            return (Object) ["field" => "input", "type" => "checkbox"];
        }
        elseif (in_array($type, ["char", "string", "uuid", "varchar"])) {
            return (Object) ["field" => "input", "type" => "text"];
        }
        elseif ($type === "enum") {
            return (Object) ["field" => "select", "type" => null];
        }
        elseif ($type === "set") {
            return (Object) ["field" => "select", "type" => "multiple"];
        }
        elseif ($type === "date") {
            return (Object)["field" => "input", "type" => "date"];
        }
        elseif (in_array($type, ["lineString", "longText", "mediumText", "multiLineString", "text"])) {
            return (Object)["field" => "textarea", "type" => null];
        }
        elseif (in_array($type, ["time", "timeTz"])) {
            return (Object)["field" => "input", "type" => "time"];
        }
        elseif (in_array($type, ["dateTime", "dateTimeTz", "timestamp", "timestampTz"])) {
            return (Object) ["field" => "input", "type" => "datetime-local"];
        }
        elseif (in_array($type, ["dateTime", "dateTimeTz", "timestamp", "timestampTz"])) {
            return (Object) ["field" => "input", "type" => "datetime-local"];
        }
        else {
            return null;
        }
    }

}

(window.webpackJsonp=window.webpackJsonp||[]).push([[19],{212:function(e,a,t){"use strict";t.r(a);var r=t(0),n=Object(r.a)({},function(){var e=this,a=e.$createElement,t=e._self._c||a;return t("ContentSlotsDistributor",{attrs:{"slot-key":e.$parent.slotKey}},[t("h1",{attrs:{id:"memory-helper"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#memory-helper","aria-hidden":"true"}},[e._v("#")]),e._v(" Memory Helper")]),e._v(" "),t("p",[e._v("This helper will streamline memory storage using key-value multidimensional array. It is used primarily to build, store and run an in-memory queue of commands and its output messages.")]),e._v(" "),t("div",{staticClass:"tip custom-block"},[t("p",[e._v('Model Helper is "macroable", which allows you to add additional methods to the MemoryHelper class at run time.')])]),e._v(" "),t("h2",{attrs:{id:"get"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#get","aria-hidden":"true"}},[e._v("#")]),e._v(" Get")]),e._v(" "),t("helper-method",{attrs:{method:"get"}},[t("pre",[t("code",[e._v("/**\n * Collect global storage array by key\n * @param string $key\n * @return Collection\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"update"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#update","aria-hidden":"true"}},[e._v("#")]),e._v(" Update")]),e._v(" "),t("helper-method",{attrs:{method:"update"}},[t("pre",[t("code",[e._v("/**\n * Update in-memory array\n * @return Memory\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"set"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#set","aria-hidden":"true"}},[e._v("#")]),e._v(" Set")]),e._v(" "),t("helper-method",{attrs:{method:"set"}},[t("pre",[t("code",[e._v("/**\n * Replace all data in keyed array\n * @param string $key\n * @param string|array $data\n * @return Memory\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"push"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#push","aria-hidden":"true"}},[e._v("#")]),e._v(" Push")]),e._v(" "),t("helper-method",{attrs:{method:"push"}},[t("pre",[t("code",[e._v("/**\n * Push data to keyed array\n * @param string $key\n * @param string|array $data\n * @return Memory\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"put"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#put","aria-hidden":"true"}},[e._v("#")]),e._v(" Put")]),e._v(" "),t("helper-method",{attrs:{method:"put"}},[t("pre",[t("code",[e._v("/**\n * Put data in keyed array using name as a second-level key\n * @param string $key\n * @param string $name\n * @param string|array $data\n * @return Memory\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"add-message"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#add-message","aria-hidden":"true"}},[e._v("#")]),e._v(" Add Message")]),e._v(" "),t("helper-method",{attrs:{method:"addMessage"}},[t("pre",[t("code",[e._v("/**\n * Add message to in-memory storage of messages\n * @param string $message\n * @param string $type\n * @return Memory\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"add-command"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#add-command","aria-hidden":"true"}},[e._v("#")]),e._v(" Add Command")]),e._v(" "),t("helper-method",{attrs:{method:"addCommand"}},[t("pre",[t("code",[e._v("/**\n * Add command to in-memory queue of commands\n * @param string $command\n * @param array $arguments\n * @return Memory\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"run-commands"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#run-commands","aria-hidden":"true"}},[e._v("#")]),e._v(" Run Commands")]),e._v(" "),t("helper-method",{attrs:{method:"runCommands"}},[t("pre",[t("code",[e._v("/**\n * Run all commands that are in in-memory queue\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"delete"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#delete","aria-hidden":"true"}},[e._v("#")]),e._v(" Delete")]),e._v(" "),t("helper-method",{attrs:{method:"delete"}},[t("pre",[t("code",[e._v("/**\n * Remove keyed array from in-memory storage\n * @param string $key\n * @return Memory\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"delete-all"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#delete-all","aria-hidden":"true"}},[e._v("#")]),e._v(" Delete All")]),e._v(" "),t("helper-method",{attrs:{method:"deleteAll"}},[t("pre",[t("code",[e._v("/**\n * Delete all data from in-memory storage, emptying all storage\n * @return Memory\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"start-a-chain"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#start-a-chain","aria-hidden":"true"}},[e._v("#")]),e._v(" Start a Chain")]),e._v(" "),t("helper-method",{attrs:{method:"startChain"}},[t("pre",[t("code",[e._v("/**\n * Start an in-memory chain or add item to it\n * @param string $uuid\n */\n")])])]),e._v(" "),t("h2",{attrs:{id:"end-a-chain"}},[t("a",{staticClass:"header-anchor",attrs:{href:"#end-a-chain","aria-hidden":"true"}},[e._v("#")]),e._v(" End a Chain")]),e._v(" "),t("helper-method",{attrs:{method:"endChain"}},[t("pre",[t("code",[e._v("/**\n * End in-memory chain of commands, if uuid is the same that started a chain.\n * Run all commands from queue and return collection of messages.\n * @param string $uuid\n * @return bool|Collection\n */\n")])])])],1)},[],!1,null,null,null);a.default=n.exports}}]);
<template>
    <div class="method">
        <p class="method-content">
            <slot></slot>
        </p>
        <div class="language-php extra-class"><pre class="language-php"><code v-html="code"></code></pre></div>
    </div>
</template>

<script>
    export default {
        name: "HelperMethod",
        props: {
            method: {
                type: String,
                required: false
            }
        },
        data() {
            return {
                methods: [],
                parameters: [],
            }
        },
        computed: {
            code () {
                const method = `<span class="token function">${this.method ? this.method : this.methods.shift()}</span>`;
                const args = this.parameters.filter(x => x.match(/^@param/)).map(x => x.replace(/^@param\s?(.*?)(\$[\w\d]+)(\s?:.*)?/, '$1<span class="token variable">$2</span>')).join(', ');
                const result = this.parameters.filter(x => x.match(/^@return/)).length ? this.parameters.filter(x => x.match(/^@return/)).shift().replace(/^@return\s?(.*?)/, '$1') : 'void';
                return `${this.$page.title.replace(' ', '')}<span class="token punctuation">:</span><span class="token punctuation">:</span>${method}<span class="token punctuation">(</span>${args}<span class="token punctuation">)</span><span class="token punctuation">:</span> ${result}`;
            }
        },
        mounted() {
            this.$slots.default.map(vnode => {
                if (vnode.elm && vnode.elm.innerText) {
                    // get and normalize content
                    const content = vnode.elm.innerText.split('\n').map(x => x.trim()).map(x => x.replace(/\/\*\*/, '').replace(/\*?\*\//, '').replace(/^\* /, '').trim()).filter(x => x !== '');
                    // get parameters
                    const parameters = content.filter(x => x.match(/^@(param|return|exception|throws)/));
                    parameters.forEach(x => this.parameters.push(x));
                    // get method if exists
                    const methods = content.filter(x => x.match(/^@method/));
                    methods.forEach(x => this.methods.push(x));
                    // get description
                    const description = content.filter(x => !methods.includes(x) && !parameters.includes(x));
                    vnode.elm.innerHTML =
                        (description.length    ? description.join('<br>') + '<br>' : '') +
                        (parameters.length     ? '<ul>' + parameters.map(x => {
                            return x.replace(/^(?:@(param|return|exception|throws))\s?([\w\d|\\ $]+)(?::(.*))?/, '<li class="method-property"><span class="method-property-type">$1</span><span class="method-property-value">$2</span><span class="method-property-description">$3</span></li>')
                        }).join('') + '</ul>' : '')
                }
                return vnode;
            });
        },
    }
</script>

<style>
    .method-content pre {
        line-height: inherit;
        padding: 0;
        margin: 0;
        background-color: transparent;
        border-radius: 0;
        overflow: auto;
        font-family: inherit;
        font-size: inherit;
        font-weight: inherit;
    }
    .method-content ul {
        list-style: none;
        padding-left: 6px;
        border-left: 2px solid #5ca2d5;
        margin-top: .5em;
        margin-bottom: .5em;
    }

    .method-property-type {
        color: #5ca2d5;
        font-weight: bold;
    }
    .method-property-value {
        color: #17242a;
        font-weight: bold;
        text-decoration: underline;
        margin-left: 8px;
        margin-right: 4px;
    }
    .method-property-description {
        color: #adadad;
    }
</style>

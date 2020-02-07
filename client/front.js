import $ from 'jquery';
import pretty from 'pretty';
import CodeMirror from 'codemirror';
import 'codemirror/mode/xml/xml';

const TASKS = {
    non_breaking_hyphen: ["@(\\w{1})-(\\w+)@i", "$1\u2011$2"],
    numbers: ["@(\\d) (\\d)@i", "$1\u00A0$2"],
    spaces_in_scales: ["@(\\d) : (\\d)@i", "$1\u00A0:\u00A0$2"],
    ordered_number: ["@(\\d\\.) ([0-9a-záčďéěíňóřšťúýž])@", "$1\u00A0$2"],
    prepositions: ["@($|;| | |\\(|\\n|>)(%keys%) @i", "$1$2\u00A0"],
    conjunctions: ["@($|;| | |\\(|\\n|>)(%keys%) @i", "$1$2\u00A0"],
    article: ["@($|;| | |\\(|\\n|>)(%keys%) @i", "$1$2\u00A0"],
    units: ["@(\\d) (%keys%)(^|[;\\.!:]| | |\\?|\\n|\\)|<|\\010|\\013|$)@i", "$1\u00A0$2$3"]
};

const KEYS = {
    cs: {
        prepositions: "do|kromě|od|u|z|ze|za|proti|naproti|kvůli|vůči|nad|pod|před|za|o|pro|mezi|přes|mimo|při|na|po|v|ve|pod|před|s|za|mezi|se|si|k|je",
        conjunctions: "a|i|o|u",
        abbreviations: "vč.|cca.|č.|čís.|čj.|čp.|fa|fě|fy|kupř.|mj.|např.|p.|pí|popř.|př.|přib.|přibl.|sl.|str.|sv.|tj.|tzn.|tzv.|zvl.",
        units: "m|m²|l|kg|h|°C|Kč|lidí|dní|%|mil"
    }, en: {
        prepositions: "aboard|about|above|across|after|against|ahead of|along|amid|amidst|among|around|as|as far as|as of|aside from|at|athwart|atop|barring|because of|before|behind|below|beneath|beside|besides|between|beyond|but|by|by means of|circa|concerning|despite|down|during|except|except for|excluding|far from|following|for|from|in|in accordance with|in addition to|in case of|in front of|in lieu of|in place of|in spite of|including|inside|instead of|into|like|minus|near|next to|notwithstanding|of|off|on|on account of|on behalf of|on top of|onto|opposite|out|out of|outside|over|past|plus|prior to|regarding|regardless of|save|since|than|through|throughout|till|to|toward|towards|under|underneath|unlike|until|up|upon|versus|via|with|with regard to|within|without",
        conjunctions: "and|at|even|about",
        article: "a|an|the",
        units: "m|m²|l|kg|h|°C|Kč|peoples|days|moths|%|miles"
    }
};

class Nbsp {

    /**  @var {CodeMirror} inputEditor */
    inputEditor;

    /**  @var {CodeMirror} inputEditor */
    outputEditor;

    /**  @var {HTMLTextAreaElement} input */
    input;
    /**  @var {HTMLTextAreaElement} input */
    output;
    /**  @var {HTMLDivElement} input */
    preview;

    tasks = {};

    constructor(input, output, preview) {
        this.input = input;
        this.output = output;
        this.preview = preview;
        this.init();
        this.update();
    }

    init() {
        let lang = document.getElementById('lang');
        lang.addEventListener('change', () => {
            this.preview.lang = lang.value;
            this.update();
        });
        this.input.value = localStorage.getItem('value');
        for (let lang of Object.keys(KEYS)) {
            this.tasks[lang] = Object.entries(TASKS).map(([name, [regex, replacement]]) => {
                if (KEYS[lang][name]) {
                    regex = regex.replace('%keys%', KEYS[lang][name]);
                }
                let matches = regex.match(/^@(?<reg>.*)@(?<flags>\w+)?$/);
                if (matches) {
                    let {reg, flags} = matches.groups;
                    return [new RegExp(reg, flags), replacement]
                } else {
                    console.log(matches, regex);
                }
            });
        }
        this.inputEditor = CodeMirror.fromTextArea(input, {
            mode: 'xml',
            lineWrapping: true,
            theme: 'darcula'
        });
        this.inputEditor.on('change', (editor) => {
            editor.save();
            this.update();
        });
        this.inputEditor.on('keyup', (editor) => {
            editor.save();
            this.update.bind(this)
        });
        this.outputEditor = CodeMirror.fromTextArea(output, {
            mode: 'xml',
            readOnly: true,
            lineWrapping: true,
            theme: 'darcula'
        });
        this.input.addEventListener('change', this.update.bind(this));
        this.input.addEventListener('keyup', () => {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(this.update.bind(this), 200);
        });
        this.output.addEventListener('change', () => {
            this.outputEditor.setValue(output.value);
        });
    }

    store() {
        localStorage.setItem('value', this.input.value);
    }

    update() {
        this.store();
        this.preview.innerHTML = this.input.value;
        this.apply(this.preview);
        this.output.value = pretty(this.preview.innerHTML);
        this.outputEditor.setValue(this.output.value);
    }

    apply(el) {
        let walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null);
        /**  @var {Node} node  */
        let node;
        while (node = walker.nextNode()) {
            let lang = (node.parentElement?.lang) || (node.parentElement.closest('[lang]')?.lang);
            node.textContent = this.nbsp(node.textContent, lang);
        }
    }

    /**
     * @param {string} text
     * @param {string} lang
     */
    nbsp(text, lang) {
        /**  @var {RegExp} regex  */
        for (let [regex, replacement] of this.tasks[lang]) {
            text = text.replace(regex, replacement);
        }
        return text;
    }
}

$(() => {
    let nbsp = new Nbsp(
        document.getElementById('input'),
        document.getElementById('output'),
        document.getElementById('preview')
    );
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && (e.key === 's')) {
            console.log('CTRL + S');
            e.preventDefault();
            nbsp.update();
            return false;
        }
    });
});


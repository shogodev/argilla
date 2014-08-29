var EzzeElFinder = EzzeElFinder || {}

EzzeElFinder.init = function(selector, options) {

    if (typeof(options) !== "object") {

        alert("ElFinder options are not specified.");
        return;
    }

    // Checking whether WYSIWYG editor is specified
    if (typeof(options['wysiwyg']) === "string") {

        // Setting callback function according to used WYSIWYG editor
        if (options['wysiwyg'] === "ckeditor") {

            options['getFileCallback'] = function(file) {
                var funcNum = window.location.search.replace(/^.*CKEditorFuncNum=(\d+).*$/, "$1");
                window.opener.CKEDITOR.tools.callFunction(funcNum, file);
                window.close();
            };
        }

        // TODO: implement your WYSIWYG editor's callback function here

        delete options['wysiwyg'];
    }

    EzzeElFinder.evaluateOptions(options);
    jQuery(selector).elfinder(options).elfinder("instance");
}

EzzeElFinder.evaluateOptions = function(options) {

    for (var optionName in options) {

        var optionValue = options[optionName];
        if (typeof(optionValue) === "string" && optionValue.indexOf("js:") === 0) {

            var expression = optionValue.replace(/^js:/, "options['" + optionName + "'] = ");
            eval(expression);
        }
        else if (typeof(optionValue) === "object") {

            this.evaluateOptions(options[optionName]);
        }
    }
}
(function($) {
    //Only execute if tinymce and tinyMCE_object are declared (ie tinymce is running)
    if (typeof window.tinymce !== "undefined" && typeof window.tinyMCE_object !== "undefined") {
        var tinymce = window.tinymce,
            tinyMCE_object = window.tinyMCE_object;

        //Configuring the 'Add footnote' button actions
        tinymce.PluginManager.add("btn_footnote", function( editor ) {
            editor.addButton( "btn_footnote", {
                text: "",
                icon: "insertdatetime",
                image: tinyMCE_object.plugin_dir + "/assets/img/icon.png",
                onclick: function() {
                    editor.windowManager.open( {
                        title: "Footnote",
                        body: [
                            {
                                type   : "textbox",
                                name   : "num",
                                label  : "Reference",
                                layout: "fit",
                                value  : ""
                            },
                            {
                                type   : "container",
                                name   : "container",
                                minWidth: 500,
                                minHeight: 300, 
                                html: "<textarea id='temptextarea'></textarea>"
                            }
                        ],
                        onsubmit: function( e ) {
                            //Building footnote with shortcode.
                            var content = tinyMCE.get('temptextarea').getContent();
                            editor.insertContent( "<span class=\"footnote-container\" data-title=\"" + e.data.num + "\" data-content=\"" + htmlentities.encode(content) + "\" id=\"fn" + Math.floor((Math.random() * 1000000) + 1) + "\"><i>[footnote title=\"" + e.data.num + "\"]" + content + "[/footnote]</i></span>");
                            tinymce.remove('#temptextarea');
                        },
                        onClose: function( e ) {
                            tinymce.remove('#temptextarea');
                        }
                    });
                    tinymce.init({
                        selector: "#temptextarea",
                        forced_root_block : "",
                        plugins: "link",
                        menubar: false,
                        min_height: 200,
                        toolbar: "bold italic | link unlink"
                    });
                }
            });
        });
        $(document).on("tinymce-editor-init", function(event, editor) {
            editor.on("click", function(event) {
                var e = event.target;
                //Detect click on the footnote inside the editor
                if(e.className == "footnote-container"){
                    //Prompt to edit footnote content
                    editor.windowManager.open( {
                        title: "Footnote",
                        body: [
                            {
                                type   : "textbox",
                                name   : "num",
                                label  : "Reference",
                                layout: "fit",
                                value  : e.dataset.title
                            },
                            {
                                type   : "container",
                                name   : "container",
                                minWidth: 500,
                                minHeight: 300, 
                                html: "<textarea id='temptextarea'>" + htmlentities.decode(e.dataset.content) + "</textarea>"
                            },
                            //Add option to delete footenot
                            {
                                type   : "button",
                                name   : "button",
                                text   : "Delete footnote",
                                onclick: function() {
                                    var a = editor.selection.getNode();
                                    var r = confirm("Delete footnote " + e.dataset.title);
                                    if (r == true) {
                                        a.parentNode.removeChild(a);
                                        editor.windowManager.close();
                                    }
                                }
                            },
                        ],
                        onsubmit: function( e ) {

                            var content = tinyMCE.get('temptextarea').getContent();

                            //Get the current footnote node (<span>)
                            var a = editor.selection.getNode();

                            //Update values
                            a.dataset.title = e.data.num;
                            a.dataset.content = content;
                            a.innerHTML = "<i>[footnote title=\"" + e.data.num + "\"]" + content + "[/footnote]</i>";

                            tinymce.remove('#temptextarea');
                        },
                        onClose: function( e ) {
                            tinymce.remove('#temptextarea');
                        }
                    });
                    tinymce.init({
                        selector: "#temptextarea",
                        forced_root_block : "",
                        plugins: "link",
                        menubar: false,
                        min_height: 200,
                        toolbar: "bold italic | link image imagetools table spellchecker"
                    });
                }
            });
        });
    } else {
        // eslint-disable-next-line no-console
        console.error("tinymce not found");
    }

    window.htmlentities = {
		/**
		 * Converts a string to its html characters completely.
		 *
		 * @param {String} str String with unescaped HTML characters
		 **/
		encode : function(str) {
			var buf = [];
			
			for (var i=str.length-1;i>=0;i--) {
				buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
			}
			
            return buf.join('');
            
            //return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		},
		/**
		 * Converts an html characterSet into its original character.
		 *
		 * @param {String} str htmlSet entities
		 **/
		decode : function(str) {
			return str.replace(/&#(\d+);/g, function(match, dec) {
				return String.fromCharCode(dec);
			});
		}
    };
    
    //Limit the article's Type of Publication taxonomy to only one choice (even tho it uses checkboxes)
    $("input[name=\"tax_input[article-type][]\"]").click(function () {
        selected = $("input[name=\"tax_input[article-type][]\"]").filter(":checked").length;
        if (selected > 1){
            $("input[name=\"tax_input[article-type][]\"]").each(function () {
                    $(this).attr("checked", false);
            });
            $(this).attr("checked", true);
        }
    });

    //Limit the article's Language taxonomy to only one choice (even tho it uses checkboxes)
    $("input[name=\"tax_input[article-language][]\"]").click(function () {
        selected = $("input[name=\"tax_input[article-language][]\"]").filter(":checked").length;
        if (selected > 1){
            $("input[name=\"tax_input[article-language][]\"]").each(function () {
                    $(this).attr("checked", false);
            });
            $(this).attr("checked", true);
        }
    });

})( window.jQuery );
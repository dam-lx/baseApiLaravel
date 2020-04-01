var $ = jQuery = require('jquery');
require('froala-editor');
require('froala-editor/js/plugins/align.min');
require('froala-editor/js/plugins/code_beautifier.min');
require('froala-editor/js/plugins/code_view.min');
require('froala-editor/js/plugins/file.min');
require('froala-editor/js/plugins/colors.min');
$(document).ready(function () {
    init_editor();
});
function init_editor(){
    $('.editor').froalaEditor({
        toolbarVisibleWithoutSelection: true,
        autofocus: true,
    });
}
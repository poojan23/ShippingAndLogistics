CKEDITOR.plugins.add('popaya', {
    init: function (editor) {
        editor.addCommand('Popaya', {
            exec: function (editor) {
                $.ajax({
                    url: 'index.php?url=common/filemanager&user_token=' + getURLVar('user_token') + '&ckeditor=' + editor.name,
                    dataType: 'html',
                    success: function (html) {
                        $('body').append(html);

                        $('#modal-image').modal('show');
                    }
                });
            }
        });

        editor.ui.addButton('Popaya', {
            label: 'Popaya',
            command: 'Popaya',
            icon: this.path + 'images/icon.png'
        });
    }
});
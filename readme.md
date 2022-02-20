# Filemanager

Filemanager is a file manager for laravel Base on `Thainph\Filemanager`.

# Installation

1. Config in app.php ` HXD\Filemanager\FileManagerServiceProvider::class,`
2. Publish config file: `php artisan vendor:publish --tag=file-manager-config`
3. Publish asset files: `php artisan vendor:publish --tag=file-manager-assets`
4. Config guards, mimes... in `config/file-manager.php`

# Use for CKeditor
Config CKeditor:
```
const options = {
    filebrowserImageBrowseUrl: {your-domain}/file-manager/browser,
    filebrowserImageUploadUrl: {your-domain}/file-manager/single-upload,
    window.CKEDITOR.replace(editor, options);
};
```
# User for selector

Embed your js like:

```
const fileManagerClient = {
    files: [],
    onSelected(payload) {
        this.files = payload;
        // Callback to your slelect event
    }
}
window.fileManagerClient = fileManagerClient;

```

`fileManagerClient.files` will store files which you selected.

# Api
Comming soon...

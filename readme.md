# Cast - Podcasting CMS add-on

Provides fieldtypes for podcasting.

Cast Audio:

A field type for uploading audio files. Files are always uploaded locally to the directory specified by the field type. Additionally, files can be uploaded to a remote FTP server (This is great for LibSyn, for instance) and served from the remote URL of your choice.

ExpressionEngine field type variables:

```
{exp:channel:entries ...params}
    {some_cast_audio_field}
        File Name: {cast:file_name}
        Mime Type: {cast:mime_type}
        File Size (bytes): {cast:file_size}
        File URL: {cast:file_url}
    {/some_cast_audio_field}
{/exp:channel:entries}
```

## License

Currently unlicensed. Haven't decided how to license Cast yet, whether free or paid, etc. And it needs some more work. So don't use it yet :smile:

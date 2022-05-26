ClassicEditor.create(document.querySelector('.js-richtexteditor'), {

    mediaEmbed: { previewsInData: true },
    alignement: {
            options: [ 'left', 'middle', 'right' ]
    },
    toolbar: {
        items: [
            'heading',
            '|',
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            'horizontalLine',
            '|',
            'indent',
            'outdent',
            '|',
            'imageUpload',
            'blockQuote',
            'mediaEmbed',
            '|',
            'undo',
            'redo',
             '|',
             "alignment:left",
             "alignment:center",
            "alignment:right"
        ]
    },
    language: 'fr',
    link: {
        addTargetToExternalLinks: true
    },
    image: {
        toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
    },
    // Configure 'mediaEmbed' with Iframely previews.
    mediaEmbed: {


        // Previews are always enabled if there’s a provider for a URL (below regex catches all URLs)
        // By default `previewsInData` are disabled, but let’s set it to `false` explicitely to be sure
        previewsInData: true,

        providers: [
            {
                // hint: this is just for previews. Get actual HTML codes by making API calls from your CMS
                name: 'video', 

                // Match all URLs or just the ones you need:
                url: /.+/,

                html: match => {
                    const url = match[ 0 ];
                    
                    // alternatively, use &key= instead of &api_key with the MD5 hash of your api_key
                    // more about it: https://iframely.com/docs/allow-origins
                           

                    return (
                        // If you need, set maxwidth and other styles for 'iframely-embed' class - it's yours to customize

                        `<video controls="" style="width: 100%;"  autoplay="" name="media" >`
                        +
                        `<source src="${ url }" type="video/mp4">`
                        +
                        `</video>` 
                       
                    );
                }
            }
        ]
    },
    licenseKey: ''

}).then(editor => {
    window.editor = editor;


}).catch(error => {
    console.error(error);
});

ClassicEditor.create(document.querySelector('.js-richtexteditor-minimal'), {

    toolbar: {
        items: [
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            'horizontalLine',
            '|',
            'indent',
            'outdent',
            '|',
            'undo',
            'redo',
            '|',
            "alignment:left",
            "alignment:center",
            "alignment:right",     
        ]
    },
    language: 'fr',
    link: {
        addTargetToExternalLinks: true
    },
    image: {
        toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
    },
    licenseKey: '',
    alignement: {
            options: [ 'left', 'middle', 'right' ]
    },

}).then(editor => {
    window.editor = editor;



}).catch(error => {
    console.error(error);
});



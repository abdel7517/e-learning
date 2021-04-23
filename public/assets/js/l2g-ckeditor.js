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
            'redo'
        ]
    },
    language: 'fr',
    link: {
        addTargetToExternalLinks: true
    },
    image: {
        toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
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
            'redo'
        ]
    },
    language: 'fr',
    link: {
        addTargetToExternalLinks: true
    },
    image: {
        toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
    },
    licenseKey: ''

}).then(editor => {
    window.editor = editor;


}).catch(error => {
    console.error(error);
});

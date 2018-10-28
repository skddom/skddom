CKEDITOR.editorConfig = function( config ) {
  config.skin = 'kama';
  config.extraPlugins = 'MediaEmbed,ncWidget';
  config.entities = false;
  config.toolbar =
  [
    ['Source','-',/*'Save',*/'NewPage','Preview','-','Templates'],
    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    '/',
    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['Link','Unlink','Anchor'],
    ['Image','Flash', 'ncWidget', 'MediaEmbed', 'Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
    '/',
    ['Styles','Format','Font','FontSize'],
    ['TextColor','BGColor'],
    ['Maximize', 'ShowBlocks','-','About']
  ];

  config.smiley_images = ['angry.gif','bigsmile.gif','cantlook.gif','cool.gif','cry.gif','doh.gif','evil.gif','eyeup.gif','grin.gif','kiss.gif','knockedout.gif','laugh.gif','lookdown.gif','no.gif','proud.gif','rolleyes.gif','sad.gif','shakefist.gif','shh.gif','sick.gif','smile.gif','stern.gif','suspicious.gif','think.gif','thumbsup.gif','undecided.gif','unsure.gif','upset.gif','wink.gif','yes.gif'];


};

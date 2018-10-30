CKEDITOR.addTemplates( 'thebuilt',
{
	// The name of the subfolder that contains the preview images of the templates.
	imagesPath :  '/netcat_template/template/thebuilt/tpl/images/' ),
 
	// Template definitions.
	templates :
		[
			{
				title: 'Текст с заголовком',
				image: '119591.png',
				description: 'Текст с заголовком',
				html:
					'<div class="mgt-header-block clearfix text-left text-black wpb_animate_when_almost_visible wpb_top-to-bottom wpb_content_element  mgt-header-block-style-2 mgt-header-texttransform-header  vc_custom_1462806743073 wpb_start_animation"><p class="mgt-header-block-title">Challenges</p><div class="mgt-header-line"></div></div>'
			},
			{
				title: 'Фото в текста',
				html:
					'<img class="size-medium wp-image-1380 alignleft" src="http://wp.magnium-themes.com/thebuilt/thebuilt-1/wp-content/uploads/2016/05/director-240x300.jpg" alt="director" width="240" height="300" srcset="http://wp.magnium-themes.com/thebuilt/thebuilt-1/wp-content/uploads/2016/05/director-240x300.jpg 240w, http://wp.magnium-themes.com/thebuilt/thebuilt-1/wp-content/uploads/2016/05/director.jpg 400w" sizes="(max-width: 240px) 100vw, 240px">'
			}
		]
});

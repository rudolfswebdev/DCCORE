// JavaScript Document
(function() {
    tinymce.PluginManager.add('s_post_grid', function(editor, url) {
		editor.addButton('s_post_grid', {
			text: '',
			tooltip: 'Smart Post Grid',
			id: 'cactus_s_post_grid',
			icon: 'layout spg-dashicons dashicons-schedule',
			onclick: function() {
				// Open window
				editor.windowManager.open({
					title: 'Smart Post Grid',
					body: [
						{type: 'textbox', name: 'title', label: 'Title'},
						
						{type: 'textbox', name: 'title_link', label: 'Title Link'},

						{type: 'listbox',
							name: 'layout',
							label: 'Layout',
							'values': [
								{text: 'Layout 1', value: '1'},
								{text: 'Layout 2', value: '2'},
								{text: 'Layout 3', value: '3'},
								{text: 'Layout 4', value: '4'},
								{text: 'Layout 5', value: '5'},
								{text: 'Layout 6', value: '6'},
								{text: 'Layout 7', value: '7'},
								{text: 'Layout 8', value: '8'},
								{text: 'Layout 9', value: '9'}
							]
						},
						
						{type: 'listbox', name: 'column', 
							label: 'Choose Column for layout 4,5,6,7,9',
							'values': [
								{text: '3 columns', value: '3'},
								{text: '2 columns', value: '2'},
								{text: '1 column', value: '1'}
						  ]
						
						},
						
						{type: 'listbox',
							name: 'filter_style',
							label: 'Show filter style',
							'values': [
								{text: 'No', value: '1'},
								{text: 'Categories Filter', value: '2'},
								{text: 'Carousel', value: '3'}
							]
						},
						
						{type: 'textbox', name: 'count', label: 'Number of items to show'},
						
						{type: 'textbox', name: 'post_type', label: 'Post type to query'},

						{type: 'listbox',
							name: 'condition',
							label: 'Condition',
							'values': [
								{text: 'Latest', value: 'latest'},
								{text: 'Most viewed*', value: 'view'},
								{text: 'Most Liked*', value: 'like'},
								{text: 'Most commented', value: 'comment'},
								{text: 'Title', value: 'title'},
								{text: 'Input(only available when using ids parameter)', value: 'input'},
								{text: 'Random', value: 'random'}
							]
						},

						{type: 'listbox',
							name: 'order',
							label: 'Order',
							'values': [
								{text: 'Descending', value: 'DESC'},
								{text: 'Ascending', value: 'ASC'}
							]
						},

						{type: 'textbox', name: 'cats', label: 'Categories'},

						{type: 'textbox', name: 'tags', label: 'Tags'},

						{type: 'textbox', name: 'ids', label: 'IDs'},

						{type: 'listbox',
							name: 'heading_style',
							label: 'Heading Style',
							'values': [
								{text: 'Gradient', value: '1'},
								{text: 'Bar', value: '2'}
							]
						},
						
						{type: 'textbox', name: 'heading_color', label: 'Heading color'},

						{type: 'textbox', name: 'heading_bg', label: 'Heading background'},
						
						{type: 'textbox', name: 'heading_icon', label: 'Class of Font Icon'},
						
						{type: 'textbox', name: 'heading_icon_color', label: 'Heading Icon Color'},
						
						{type: 'listbox',
							name: 'show_category_tag',
							label: 'Show category tag',
							'values': [
								{text: 'Yes', value: '1'},
								{text: 'No', value: '0'}
							]
						},
						
						{type: 'listbox',
							name: 'show_meta',
							label: 'Show Meta',
							'values': [
								{text: 'Yes', value: '1'},
								{text: 'No', value: '0'}
							]
						},
						
						{type: 'textbox', name: 'view_all_text', label: 'Change View All text', value: ''},

					],
					onsubmit: function(e) {
						// Insert content when the window form is submitted
						//var uID =  Math.floor((Math.random()*100)+1);
						var title = e.data.title;
						var title_link = e.data.title_link;
						var layout = e.data.layout;
						var column = e.data.column;
						var count = e.data.count;
						var post_type = e.data.post_type;
						var condition = e.data.condition;
						var order = e.data.order;
						var cats = e.data.cats;
						var tags = e.data.tags;
						var ids = e.data.ids;
						var items_per_page = e.data.items_per_page;
						var filter_style  = e.data.filter_style;
						var heading_style  = e.data.heading_style;
						var heading_color  = e.data.heading_color;
						var heading_bg  = e.data.heading_bg;
						var heading_icon  = e.data.heading_icon;
						var heading_icon_color  = e.data.heading_icon_color;
						var show_category_tag  = e.data.show_category_tag;
						var show_meta  = e.data.show_meta;
						var view_all_text  = e.data.view_all_text;
						
						editor.insertContent('[s_post_grid title="'+title+'" title_link="'+title_link+'" layout="'+layout+'" column="'+column+'" count="'+count+'" post_type="'+post_type+'" condition="'+condition+'" order="'+order+'" cats="'+cats+'" tags="'+tags+'" ids="'+ids+'" filter_style="'+filter_style+'" heading_style="'+heading_style+'" heading_color="'+heading_color+'" heading_bg="'+heading_bg+'" heading_icon="'+heading_icon+'" heading_icon_color="'+heading_icon_color+'" show_category_tag="'+show_category_tag+'" show_meta="'+show_meta+'" view_all_text="'+view_all_text+'"][/s_post_grid]');
					}
				});
			}
		});
	});
})();
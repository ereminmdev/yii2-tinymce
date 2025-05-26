tinymce.PluginManager.add('appwidget', function (editor) {
    const widgets = [
        {title: '', code: ''},
        ...(editor.getParam('appwidget_blocks') || [])
    ];

    editor.on('PreInit', () => {
        editor.contentStyles.push(`
.mce-content-body .app-widget {
    margin: 1px;
    padding: .5em;
    color: #333;
    font-size: .9em;
    font-family: system-ui;
    background: #eef;
    border: 1px solid #00000026;
    border-radius: .375em;
    cursor: pointer !important;
}
        `);
    });

    function openWidgetDialog(targetElement = null) {
        const currentCode = targetElement?.textContent || '';
        const matched = widgets.find(w => w.code === currentCode || w.title === currentCode);
        const initialValue = matched ? matched.code : '';

        editor.windowManager.open({
            title: targetElement ? 'Редактировать виджет' : 'Вставить виджет',
            body: {
                type: 'panel',
                items: [
                    {
                        type: 'selectbox',
                        name: 'widget',
                        label: 'Выберите виджет',
                        items: widgets.map(w => ({text: w.title, value: w.code}))
                    }
                ]
            },
            initialData: {widget: initialValue},
            buttons: [
                {type: 'cancel', text: 'Отмена'},
                {type: 'submit', text: 'ОК', primary: true}
            ],
            onSubmit: (api) => {
                const data = api.getData();

                if (data.widget === '') {
                    if (targetElement) {
                        targetElement.remove();
                    }
                    api.close();
                    return;
                }

                const selected = widgets.find(w => w.code === data.widget);
                if (!selected) return;

                const html = `<div><div class="app-widget mceNonEditable">${selected.title}</div></div>`;

                if (targetElement) {
                    targetElement.outerHTML = html;
                } else {
                    editor.insertContent(html);
                }

                api.close();
            }
        });
    }

    function replaceToTitle(editor) {
        let content = editor.getContent();

        content = content.replaceAll('app-widget mceNonEditable', '');

        const blocks = editor.getParam('appwidget_blocks') || [];
        blocks.forEach((block) => {
            content = content.replaceAll(block.code, `<div class="app-widget mceNonEditable">${block.title}</div>`);
        });

        editor.setContent(content);
    }

    function replaceToCode(e, editor) {
        const blocks = editor.getParam('appwidget_blocks') || [];
        blocks.forEach((block) => {
            e.content = e.content.replaceAll(`<div class="app-widget mceNonEditable">${block.title}</div>`, block.code);
        });
    }

    editor.on('init', () => replaceToTitle(editor));
    editor.on('change', () => replaceToTitle(editor));
    editor.on('GetContent', (e) => replaceToCode(e, editor));

    editor.on('click', function (e) {
        const el = e.target.closest('.app-widget');
        if (el) {
            e.preventDefault();
            openWidgetDialog(el);
        }
    });

    editor.ui.registry.addMenuItem('insertappwidget', {
        text: 'Вставить виджет',
        icon: 'addtag',
        onAction: () => openWidgetDialog(),
    });

    editor.ui.registry.addMenuItem('appwidget', {
        text: 'Виджет',
        icon: 'addtag',
        onAction: () => openWidgetDialog(),
    });

    editor.ui.registry.addButton('appwidget', {
        tooltip: 'Вставить виджет',
        icon: 'addtag',
        onAction: () => openWidgetDialog(),
    });
});

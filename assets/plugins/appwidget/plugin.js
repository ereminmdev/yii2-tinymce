tinymce.PluginManager.add('appwidget', function (editor) {
    const widgets = [
        {title: '', code: ''},
        ...(editor.getParam('appwidget_blocks') || [])
    ];

    function openWidgetDialog(targetElement = null) {
        const currentCode = targetElement?.textContent || '';
        const matched = widgets.find(w => w.code === currentCode);
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

                const html = `<div class="app-widget mceNonEditable">${selected.code}</div>`;

                if (targetElement) {
                    targetElement.outerHTML = html;
                } else {
                    editor.insertContent(html);
                }

                api.close();
            }
        });
    }

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

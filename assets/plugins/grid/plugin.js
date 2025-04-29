tinymce.PluginManager.add('grid', function (editor, url) {
    onAction = function () {
        const currentData = {
            cols: '2',
            rows: '1',
            gap: '2em',
            justify: 'stretch',
            align: 'start',
        };

        editor.windowManager.open({
            title: 'Вставить сетку',
            body: {
                type: 'panel',
                items: [
                    {
                        type: 'grid',
                        columns: 2,
                        items: [
                            {
                                type: 'selectbox',
                                name: 'cols',
                                label: 'Количество колонок',
                                items: [
                                    {value: '2', text: '2'},
                                    {value: '3', text: '3'},
                                    {value: '4', text: '4'},
                                    {value: '6', text: '6'},
                                    {value: '12', text: '12'},
                                ]
                            },
                            {type: 'input', name: 'rows', label: 'Количество строк', inputMode: 'numeric'},
                            {type: 'input', name: 'gap', label: 'Отступы между ячейками'},
                            {
                                type: 'htmlpanel',
                                html: '<div class="tox-label" style="padding-top: 2em"><a href="https://developer.mozilla.org/ru/docs/Web/CSS/gap" target="_blank">Отступы gap</a></div>'
                            },
                            {
                                type: 'selectbox',
                                name: 'justify',
                                label: 'Горизонтальное выравнивание',
                                items: [
                                    {value: 'start', text: 'Слева'},
                                    {value: 'center', text: 'По центру'},
                                    {value: 'end', text: 'Справа'},
                                    {value: 'stretch', text: 'Растянуть'},
                                ]
                            },
                            {
                                type: 'selectbox',
                                name: 'align',
                                label: 'Вертикальное выравнивание',
                                items: [
                                    {value: 'start', text: 'Сверху'},
                                    {value: 'center', text: 'По центру'},
                                    {value: 'end', text: 'Снизу'},
                                    {value: 'stretch', text: 'Растянуть'},
                                ]
                            },
                        ],
                    },
                ]
            },
            initialData: currentData,
            buttons: [
                {type: 'cancel', text: 'Отмена'},
                {type: 'submit', text: 'Вставить', primary: true},
            ],
            onSubmit: function (api) {
                const data = api.getData();
                const cols = parseInt(data.cols) || 2;
                const rows = parseInt(data.rows) || 1;
                const gap = data.gap || '2em';
                const justify = data.justify || 'stretch';
                const align = data.align || 'start';

                let colClass = 'g-h6';
                if (cols == 3) colClass = 'g-h4';
                else if (cols == 4) colClass = 'g-h3';
                else if (cols == 6) colClass = 'g-h2';
                else if (cols == 12) colClass = 'g-h1';

                const totalItems = cols * rows;

                let gridHTML = `<div class="g" style="gap: ${gap}; justify-items: ${justify}; align-items: ${align};">`;

                for (let i = 1; i <= totalItems; i++) {
                    gridHTML += `<div class="${colClass}">${i}</div>`;
                }

                gridHTML += `</div><p>&nbsp;</p>`;

                editor.insertContent(gridHTML);
                api.close();
            }
        });
    };

    editor.ui.registry.addMenuItem('insertgrid', {
        text: 'Вставить сетку',
        icon: 'table',
        onAction: onAction,
    });

    editor.ui.registry.addMenuItem('grid', {
        text: 'Сетка',
        icon: 'table',
        onAction: onAction,
    });

    editor.ui.registry.addButton('insertgrid', {
        text: 'Сетка',
        onAction: onAction,
    });
});

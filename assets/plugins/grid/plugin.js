tinymce.PluginManager.add('grid', function (editor, url) {
    onAction = function () {
        const currentData = {
            cols: '2',
            rows: '1',
            gap: '20',
            justify: 'stretch',
            align: 'start',
        };

        editor.windowManager.open({
            title: 'Вставить сетку',
            body: {
                type: 'panel',
                items: [
                    {
                        type: 'selectbox',
                        name: 'cols',
                        label: 'Выбери сетку',
                        items: [
                            {value: '2', text: '2 колонки'},
                            {value: '3', text: '3 колонки'},
                            {value: '4', text: '4 колонки'},
                            {value: '6', text: '6 колонок'},
                        ]
                    },
                    {type: 'input', name: 'rows', label: 'Количество строк', inputMode: 'numeric'},
                    {type: 'input', name: 'gap', label: 'Расстояние между ячейками (px)', inputMode: 'numeric'},
                    {
                        type: 'selectbox',
                        name: 'justify',
                        label: 'Горизонтальное выравнивание элементов',
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
                        label: 'Вертикальное выравнивание элементов',
                        items: [
                            {value: 'start', text: 'Сверху'},
                            {value: 'center', text: 'По центру'},
                            {value: 'end', text: 'Снизу'},
                            {value: 'stretch', text: 'Растянуть'},
                        ]
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
                const gap = parseInt(data.gap) || 10;
                const justify = data.justify || 'stretch';
                const align = data.align || 'start';

                let colClass = 'g-h6';
                if (cols == 3) colClass = 'g-h4';
                else if (cols == 4) colClass = 'g-h3';
                else if (cols == 6) colClass = 'g-h2';

                const totalItems = cols * rows;

                let gridHTML = `<div class="g" style="gap: ${gap}px; justify-items: ${justify}; align-items: ${align};">`;

                for (let i = 1; i <= totalItems; i++) {
                    gridHTML += `<div class="${colClass}">Колонка ${i}</div>`;
                }

                gridHTML += `</div><br>`;

                editor.insertContent(gridHTML);
                api.close();
            }
        });
    };

    editor.ui.registry.addMenuItem('insertgrid', {
        text: 'Сетка',
        icon: 'table',
        onAction: onAction,
    });

    editor.ui.registry.addButton('insertgrid', {
        text: 'Сетка',
        onAction: onAction,
    });
});

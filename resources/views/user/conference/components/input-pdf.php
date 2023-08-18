<html lang="pt-BR">
<head>
    <style>
        @page {
            margin: 20px;
        }

        div.page-break {
            page-break-after: always;
        }

        img.logo {
            position: absolute!important;
        }

        h1.title {
            text-align: center;
        }

        table.table {
            display: table;
            width: 100%;
            margin-bottom: 2rem;
            background-color: transparent;
            border-collapse: collapse;
            text-indent: initial;
            border-spacing: 2px;
            border-spacing: 2px;
            border-color: grey;
        }

        table.table thead th {
            color: white;
            padding: .50rem;
            vertical-align: middle!important;
            border-bottom: 2px solid #dee2e6;
        }

        table.table > thead {
            background-color: #0275d8;
        }

        table.table td {
            text-align: left;
            padding: .50rem;
            vertical-align: middle!important;
            border-top: 1px solid #dee2e6;
        }

        table.table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    <link rel="shortcut icon" href="<?= $shortcutIcon ?>" type="image/png">
    <title><?= _('Etiqueta de Entrada') ?></title>
</head>
<body>
    <?php for($i = 0; $i < count($dbPallets); $i++): ?>
    <?php if($i != 0): ?>
    <div class="page-break"></div>
    <?php endif; ?>
    <div style="padding: 1.25rem;">
        <div>
            <img src="<?= $logo ?>" height="60px" class="logo">
        </div>

        <h1 class="title"><?= sprintf(_('Etiqueta de Entrada'), $dbPallets[$i]->code) ?></h1>
        <table class="table">
            <thead>
                <th colspan="2"><?= _('Informações do Pallet') ?></th>
            </thead>
            
            <tbody>
                <tr>
                    <td><?= _('Número do Pallet') ?></td>
                    <td><?= $dbPallets[$i]->code ?></td>
                </tr>
                <tr>
                    <td><?= _('Data de Entrada') ?></td>
                    <td><?= $dbPallets[$i]->getStorageDateTime()->format('d/m/Y') ?></td>
                </tr>
                <tr>
                    <td><?= _('Horário de Entrada') ?></td>
                    <td><?= $dbPallets[$i]->getStorageDateTime()->format('H:i:s') ?></td>
                </tr>
                <tr>
                    <td><?= _('Rua') ?></td>
                    <td><?= $dbPallets[$i]->street_number ?></td>
                </tr>
                <tr>
                    <td><?= _('Posição') ?></td>
                    <td><?= $dbPallets[$i]->position ?></td>
                </tr>
                <tr>
                    <td><?= _('Altura') ?></td>
                    <td><?= $dbPallets[$i]->height ?></td>
                </tr>
                <tr>
                    <td><?= _('Ordem de Serviço') ?></td>
                    <td><?= $dbOperation->order_number ?></td>
                </tr>
                <tr>
                    <td><?= _('Código do Produto') ?></td>
                    <td><?= $dbPallets[$i]->product->ean ?></td>
                </tr>
                <tr>
                    <td><?= _('Nome do Produto') ?></td>
                    <td><?= $dbPallets[$i]->product->name ?></td>
                </tr>
                <tr>
                    <td><?= _('Embalagem') ?></td>
                    <td><?= $dbPallets[$i]->package ?></td>
                </tr>
                <tr>
                    <td><?= _('Quantidade de Caixas Físicas') ?></td>
                    <td><?= $dbPallets[$i]->physic_boxes_amount ?></td>
                </tr>
                <tr>
                    <td><?= _('Quantidade de Unidades') ?></td>
                    <td><?= $dbPallets[$i]->units_amount ?></td>
                </tr>
                <tr>
                    <td><?= _('Tipo de Serviço') ?></td>
                    <td><?= $dbPallets[$i]->getServiceType() ?></td>
                </tr>
                <tr>
                    <td><?= _('Altura do Pallet') ?></td>
                    <td><?= $dbPallets[$i]->pallet_height ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endfor; ?>
</body>
</html>
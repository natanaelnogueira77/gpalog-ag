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
    <title><?= _('Saída de Pallets') ?></title>
</head>
<body>
    <div style="padding: 1.25rem;">
        <div>
            <img src="<?= $logo ?>" height="60px" class="logo">
        </div>

        <h1 class="title"><?= sprintf(_('Saída de Pallets - OC %s'), $dbOperation->order_number) ?></h1>
        <table class="table">
            <thead>
                <th colspan="8"><?= _('Informações da Saída') ?></th>
            </thead>
            
            <tbody>
                <tr>
                    <td colspan="4"><?= _('ID de Separação') ?></td>
                    <td colspan="4"><?= $dbOutput->id ?></td>
                </tr>
                <tr>
                    <td colspan="4"><?= _('Data de Separação') ?></td>
                    <td colspan="4"><?= $dbOutput->getCreatedAtDateTime()->format('d/m/Y') ?></td>
                </tr>
                <tr>
                    <td colspan="4"><?= _('Horário de Separação') ?></td>
                    <td colspan="4"><?= $dbOutput->getCreatedAtDateTime()->format('H:i:s') ?></td>
                </tr>
            </tbody>

            <thead>
                <th style="text-align: center;"><?= _('Nº do Pallet') ?></th>
                <th style="text-align: center;"><?= _('Rua') ?></th>
                <th style="text-align: center;"><?= _('Posição') ?></th>
                <th style="text-align: center;"><?= _('Altura') ?></th>
                <th style="text-align: center;"><?= _('OC') ?></th>
                <th style="text-align: center;"><?= _('Nome do Produto') ?></th>
                <th style="text-align: center;"><?= _('EAN') ?></th>
                <th style="text-align: center;"><?= _('Serviço') ?></th>
            </thead>
            
            <tbody>
                <?php 
                if($dbPallets): 
                    foreach($dbPallets as $dbPallet):
                    ?>
                    <tr>
                        <td style="text-align: center;"><?= $dbPallet->code ?></td>
                        <td style="text-align: center;"><?= $dbPallet->street_number ?></td>
                        <td style="text-align: center;"><?= $dbPallet->position ?></td>
                        <td style="text-align: center;"><?= $dbPallet->height ?></td>
                        <td style="text-align: center;"><?= $dbOperation->order_number ?></td>
                        <td style="text-align: center;"><?= $dbPallet->product->name ?></td>
                        <td style="text-align: center;"><?= $dbPallet->product->ean ?></td>
                        <td style="text-align: center;"><?= $dbPallet->getServiceType() ?></td>
                    </tr>
                    <?php 
                    endforeach;
                endif;    
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
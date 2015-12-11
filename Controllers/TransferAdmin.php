<?php
@session_start();
require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/SQL.php");

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Transfer.php");

require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
$acc = new Account($db);

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Date.class.php");
$data = new Date;
?>
<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a>
                    <i class="fa fa-arrow-circle-o-right"></i>
                    ADMIN
                </a>
            </h4>
        </div>
        <div class="panel-body">

            <table class="table table-bordered table-condensed table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Login</th>
                        <th>Destino</th>
                        <th>Valor</th>
                        <th>Moeda</th>
                        <th>Data</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $db->Query("SELECT TOP 10 * FROM Z_TransferLog ORDER BY date DESC");
                    $ADMResult = $db->NumRows();
                    if ($ADMResult > 0):
                        for ($i = 0; $i < $ADMResult; $i++):
                            $ADMData = $db->GetRow();
                            $NewTypeName = getCurrency($ADMData['type_dest']);
                            $i + 1;
                                echo "<tr align=\"center\">";
                                echo "<td> {$i} </td>";
                                echo "<td> {$ADMData['memb___id']} </td>";
                                echo "<td> {$ADMData['memb___id_dest']} </td>";
                                echo "<td> {$ADMData['value_dest']} </td>";
                                echo "<td> {$NewTypeName} </td>";
                                echo "<td> " . date('d/m/Y H:i:s', strtotime($ADMData['date'])) . " </td>";
                                echo "<td> {$ADMData['ip']} </td>";
                            echo "</tr>";
                        endfor;
                    else:
                        echo "<tr>";
                        echo "<td colspan='6'> <div class='alert alert-info'><b>{$acc->memb_name}</b> não existe transferência ainda no banco de dados!</div> </tr>";
                        echo "</tr>";
                    endif;
                    ?>
            </table>
            <?php
//            echo "Existe {$ADMResult} transferência no momento a ultima foi em : " . date('d/m/Y H:i:s', strtotime($ADMData['date'])) . " com o IP: " . $ADMData['ip'];
            ?>
        </div>
    </div>
</div>
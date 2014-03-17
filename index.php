<?php
include_once './global.config.php';
include_once './Core/PhpCs.php';
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Welcome to phpcs</title>
        <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>-->        
        <style>
            pre{
                background-color: lightgray;
            }
        </style>
    </head>
    <body>
        <?php
        $phpcs = new Core\PhpCs();
        $phpcs->set_mainRootDir(ROOT_DIRECTORY);
        $currentDirPath = ROOT_DIRECTORY;
        if (!empty($_GET)) {
            $currentDirPath = $_GET['original_path'];
            if (isset($_GET['sniff_file'])) {
                $sniffFile = $phpcs->getFileReport($_GET['sniff_file']);
            } else if (isset($_GET['goto_path'])) {
                $np = explode('/', $_GET['goto_path']);
                if ($np[count($np) - 1] == '..') {
                    unset($np[count($np) - 1]);
                    unset($np[count($np) - 1]);
                }
                $currentDirPath = implode('/', $np);
            }
        }
        $scanDirectory = $phpcs->scanDirectory($currentDirPath);
        ?>

        <h4>Current Path: <?php echo $currentDirPath; ?></h4>        
        <?php
        if (!empty($scanDirectory)) {
            ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>File/Dir</th>
                        <th>RealPath</th>
                        <th>Action</th>
                    </tr>   
                </thead>
                <tbody>
                    <?php
                    foreach ($scanDirectory['scans'] as $value) {
                        $str = "<tr><td>" . $value['name'] . "</td><td>" . $value['realPath'] . "</td>";
                        $str .= "<td>";
                        if ($value['isDir']) {
                            $url = '?' . http_build_query(array(
                                        'original_path' => $scanDirectory['path'][0],
                                        'goto_path' => $value['realPath']
                            ));
//                                $str .= 'Go Inside';
                            $v = ($value['name'] == '..') ? 'Back' : 'Go inside';
                            if ($value['realPath'] != SERVER_ROOT . '/..') {
                                $str .= "<a href='$url'>$v</a>";
                            }
                        } else if ($value['isPhp']) {
                            $url = '?' . http_build_query(array(
                                        'original_path' => $scanDirectory['path'][0],
                                        'sniff_file' => $value['realPath']
                            ));
                            $str .= "<a href='$url'>Sniff Code</a>";
                        }
                        $str .= "</td>";
                        $str .= "</tr>";
                        echo $str;
                    }
                    ?>
                </tbody>                        
            </table>
            <?php
        }
        ?>        
        <br><br>
        <?php if (isset($sniffFile)) { ?>
            <b>Sniffed Code: </b> <?php echo $sniffFile['file_path']; ?>                
            <pre>
                <?php
                if (empty($sniffFile['report'])) {
                    echo 'Woohoo...!!! Your code is perfectly as per Zend Standards.';
                }else{
                    echo $sniffFile['report'];
                }
                ?>
            </pre>
        <?php }
        ?>        
    </body>
</html>

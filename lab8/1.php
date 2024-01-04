<!DOCTYPE html>
<head>
  <title>Таблица умножения</title>
  <link rel = "stylesheet" href = "\Labs\lab8\style.css">
</head>
<body>
<table>
    <tr>
        <?php for($i=1;$i<=5;$i++):?>
            <td>
                <?php for($j=1;$j<=10;$j++):?>
                    <div><?php echo $i?>x<?php echo $j?>=<?php echo $i*$j?></div>
                <?php endfor;?> 
            </td>
        <?php endfor;?> 
    </tr> 
    <tr>
        <?php for($i=6;$i<=10;$i++):?>
            <td>
                <?php for($j=1;$j<=10;$j++):?>
                    <div><?php echo $i?>x<?php echo $j?>=<?php echo $i*$j?></div>
                <?php endfor;?>
             </td> 
        <?php endfor;?>
    </tr> 
</table>
</body>
</html>
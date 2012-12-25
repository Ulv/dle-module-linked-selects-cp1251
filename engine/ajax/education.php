<?php
/**
* выбор специальность/регион/ВУЗ (связанные списки, ajax)
*
* модуль для dle 9.6, часть ajax
*
* author: Vladimir Chmil <ulv8888@gmail.com>
* link:   https://github.com/Ulv/dle-module-linked-selects.git 
*/

header('Content-type: application/json');
include ('../api/api.class.php');

if (isset($_GET["specid"]) || isset($_GET["regid"]) || isset($_GET["vuzid"]))
{

    // выбираем регионы по коду специальности
    if (isset($_GET["specid"]) && !isset($_GET["regid"])) {
        $spec_id = preg_replace("#[^0-9]#","", substr($_GET["specid"], 0, 4));
        $sql = $db->query("SELECT distinct region.id as id, region.title as title FROM region, spectovuz WHERE region.id = spectovuz.region_id and spectovuz.spec_id=".$spec_id." order by region.title asc");

        $result = array();
        while ($row = $db->get_row($sql)) {
            $result[] = array("id"=>$row["id"], "title"=>$row["title"]);
        }

        if (!empty($result)) {
            echo json_encode(array('status'=>'ok', 'data'=>$result));
        } else {
            echo json_encode(array('status'=>'err', 'data'=>null));
        }
    }


    // выбираем вузы по региону и специальности
    if (isset($_GET["specid"]) && isset($_GET["regid"])) {
        $spec_id = preg_replace("#[^0-9]#","", substr($_GET["specid"], 0, 4));
        $reg_id  = preg_replace("#[^0-9]#","", substr($_GET["regid"], 0, 4));

        $sql = $db->query("SELECT distinct vuz.id as id, vuz.title as title, vuz.link as link FROM vuz, spectovuz WHERE vuz.id = spectovuz.vuz_id and spectovuz.spec_id=".$spec_id." and spectovuz.region_id=".$reg_id." order by vuz.title asc");

        $result = array();
        while ($row = $db->get_row($sql)) {
        $result[] = array("id"=>$row["id"], "title"=>$row["title"], "link"=>$row["link"]);
        }

        if (!empty($result)) {
            echo json_encode(array('status'=>'ok', 'data'=>$result));
        } else {
            echo json_encode(array('status'=>'err', 'data'=>null));
        }

    }


    // url новости вуза

    /*if (isset($_GET["vuzid"]) && !isset($_GET["specid"]) && !isset($_GET["regid"])) {
        $vuz_id = preg_replace("#[^0-9]#","", substr($_GET["vuzid"], 0, 4));

        $sql = $db->query("select link from vuz where id=".$vuz_id);
        while ($row = $db->get_row($sql)) {
            echo json_encode(array('status'=>'ok', 'data'=>$row['link']));
        }
    }*/
} else {
    header('Content-type: application/json');
    $sql = $db->query("select id, link from vuz order by id");
    while ($row = $db->get_row($sql)) {
        $data[] = array(
            'id'=>$row["id"],
            'link'=>$row["link"]
        );
    }

    echo json_encode(array(
        'status'=>'ok', 
        'data'=>$data
    ));
    // get all links
}
    /*else {
    echo "Incorrect parameters"
}*/
?>

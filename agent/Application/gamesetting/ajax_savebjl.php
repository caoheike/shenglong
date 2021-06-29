<?php
include(dirname(dirname(dirname(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__))))) . "/Public/config.php");
if($_GET['form'] == 'form1'){
    $zhuang = $_POST['zhuang'];
    $xian = $_POST['xian'];
    $he = $_POST['he'];
    $zhuangdui = $_POST['zhuangdui'];
    $xiandui = $_POST['xiandui'];
    $anydui = $_POST['anydui'];
    update_query("fn_lottery9", array("zhuang" => $zhuang, 'xian' => $xian, 'he' => $he, 'zhuangdui' => $zhuangdui, 'xiandui' => $xiandui, 'anydui' => $anydui), array('roomid' => $_SESSION['agent_room']));
    echo '<script>alert("保存成功~感谢使用!"); window.location.href="/houtai/index.php?m=g_setting&g=bjl";</script>';
}elseif($_GET['form'] == 'form2'){
    $zhuang_min = $_POST['zhuang_min'];
    $xian_min = $_POST['xian_min'];
    $he_min = $_POST['he_min'];
    $zhuangdui_min = $_POST['zhuangdui_min'];
    $xiandui_min = $_POST['xiandui_min'];
    $anydui_min = $_POST['anydui_min'];
	
	
    $zhuang_max = $_POST['zhuang_max'];
    $xian_max = $_POST['xian_max'];
    $he_max = $_POST['he_max'];
    $zhuangdui_max = $_POST['zhuangdui_max'];
    $xiandui_max = $_POST['xiandui_max'];
    $anydui_max = $_POST['anydui_max'];
    update_query("fn_lottery9", array("zhuang_min" => $zhuang_min, 'xian_min' => $xian_min, 'he_min' => $he_min, 'zhuangdui_min' => $zhuangdui_min, 'xiandui_min' => $xiandui_min, 'anydui_min' => $anydui_min, 'zhuang_max' => $zhuang_max, 'xian_max' => $xian_max, 'he_max' => $he_max, 'zh_max' => $zh_max, 'zhuangdui_max' => $zhuangdui_max, 'xiandui_max' => $xiandui_max, 'anydui_max' => $anydui_max), array('roomid' => $_SESSION['agent_room']));
    echo '<script>alert("保存成功~感谢使用!"); window.location.href="/houtai/index.php?m=g_setting&g=bjl";</script>';
}elseif($_GET['form'] == 'form3'){
    $open = $_POST['opengame'] == 'on' ? 'true' : 'false';
    $fengtime = $_POST['fengtime'];
    update_query("fn_lottery9", array("fengtime" => $fengtime, 'gameopen' => $open), array('roomid' => $_SESSION['agent_room']));
    echo '<script>alert("保存成功~感谢使用!"); window.location.href="/houtai/index.php?m=g_setting&g=bjl";</script>';
}elseif($_GET['form'] == 'form4'){
    $content = $_POST['customcont'];
    update_query("fn_lottery9", array("rules" => $content), array('roomid' => $_SESSION['agent_room']));
    echo '<script>alert("保存成功~感谢使用!"); window.location.href="/houtai/index.php?m=g_setting&g=bjl";</script>';
}
?>
<?php

// JP7's PHP Application Functions
// Copyright 2004-2006 JP7
// http://jp7.com.br
// Versão 0.05 - 2006/08/29

// jp7_app_createSelect() (2004/04/29)
function jp7_app_createSelect($name, $label, $div, $start, $finish, $value, $xtra = '')
{
    $S = ''.
    '<select name="'.$name.'"'.(($xtra) ? ' '.$xtra : '').' label="'.$label.'">'.
    '<option value="">'.$label.'</option>'.
    '<option value="">'.$div.'</option>';
    for ($i = $start;$i <= $finish;$i++) {
        if ($i < 10) {
            $i = '0'.$i;
        }
        $S .= '<option value="'.$i.'"'.(($i == $value) ? ' selected="selected"' : '').'>'.$i.'</option>';
    }
    $S .= '</select>';

    return $S;
}

// jp7_app_createSelect_date() (2007/05/25 by JP)
function jp7_app_createSelect_date($var, $time_xtra = '', $s = false, $i = false, $readonly = '', $xtra = '', $obligatory = '', $valor = null)
{
    global $l_dia, $l_mes, $l_ano, $l_hora, $l_min, $jp7_app;
    if ($jp7_app) {
        $lang = new jp7_lang('pt-br', true);
    } else {
        global $lang;
    }
    if (is_null($valor)) {
        $valor = $GLOBALS[$var];
    }
    $date = jp7_date_split($valor);
    if ($i !== false) {
        $i = '['.$i.']';
    }
    if ($GLOBALS['interadmin_visualizar']) {
        if ($date['d'] != '00') {
            return ''.
            '<table border="0" cellspacing="0" cellpadding="0">'.
                '<tr>'.
                    '<td>'.(($date['d'] != '00') ? $date['d'] : '').'</td>'.
                    '<td>&nbsp;/&nbsp;</td>'.
                    '<td>'.(($date['m'] != '00') ? $date['m'] : '').'</td>'.
                    '<td>&nbsp;/&nbsp;</td>'.
                    '<td>'.(($date['Y'] != '0000') ? $date['Y'] : '').'</td>'.
                    '<td'.(($time_xtra) ? ' '.$time_xtra : '').' nowrap>&nbsp;-&nbsp;</td>'.
                    '<td'.(($time_xtra) ? ' '.$time_xtra : '').'>'.(($date['H']) ? $date['H'] : '').'</td>'.
                    '<td'.(($time_xtra) ? ' '.$time_xtra : '').'>&nbsp;:&nbsp;</td>'.
                    '<td'.(($time_xtra) ? ' '.$time_xtra : '').'>'.(($date['i']) ? $date['i'] : '').'</td>'.
                    (($s) ? '<td'.(($time_xtra) ? ' '.$time_xtra : '').'>&nbsp;:&nbsp;</td>' : '').
                    (($s) ? '<td><input '.(($obligatory) ? ' obligatory="yes"' : '').' type="text" name="'.$var.'_s'.$i.'" value="'.$date['s'].'" style="color:#ccc;width:20px"></td>' : '').
                '</tr>'.
            '</table>';
        } else {
            return 'N/D';
        }
    } elseif (strpos($xtra, 'nocombo_') !== false) {
        $day = '<td><input '.(($obligatory) ? ' obligatory="yes"' : '').' type="text" name="'.$var.'_d'.$i.'" maxlength="2" value="'.(($date[d] != '00' && $valor) ? $date[d] : $l_dia).'" '.$readonly.' helpvalue="'.$l_dia.'" style="width:3em'.(($date[d] == '00' || !$valor) ? ';color:#ccc;font-style:italic' : '')."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\" onkeyup=\"DFchangeField(this, event)\" /></td>".
                '<td>&nbsp;/&nbsp;</td>';
        $month = '<td><input '.(($obligatory) ? ' obligatory="yes"' : '').' type="text" name="'.$var.'_m'.$i.'" maxlength="2" value="'.(($date[m] != '00' && $valor) ? $date[m] : $l_mes).'" '.$readonly.' helpvalue="'.$l_mes.'" style="width:3em'.(($date[m] == '00' || !$valor) ? ';color:#ccc;font-style:italic' : '')."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\" onkeyup=\"DFchangeField(this, event)\" /></td>".
                '<td>&nbsp;/&nbsp;</td>';

        return ''.
        '<table border="0" cellspacing="0" cellpadding="0">'.
            '<tr>'.
                (($lang->lang == 'en') ? $month.$day : $day.$month).
                '<td><input '.(($obligatory) ? ' obligatory="yes"' : '').' type="text" name="'.$var.'_Y'.$i.'" maxlength="4" value="'.(($date['Y'] != '0000' && $valor) ? $date['Y'] : $l_ano).'" '.$readonly.' helpvalue="'.$l_ano.'" style="width:5em'.(($date[Y] == '0000' || !$valor) ? ';color:#ccc;font-style:italic' : '')."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\"".((!$time_xtra) ? ' onkeyup="DFchangeField(this, event)"' : '').' /></td>'.
                '<td'.(($time_xtra) ? ' '.$time_xtra : '').' nowrap>&nbsp;-&nbsp;</td>'.
                '<td><input '.(($obligatory) ? ' obligatory="yes"' : '').' type="text" name="'.$var.'_H'.$i.'" maxlength="2" value="'.(($date['H'] && $valor != '0000-00-00 00:00:00') ? $date['H'] : $l_hora).'" '.$readonly.' helpvalue="'.$l_hora.'" style="width:3em'.((!$date['H'] || $valor == '0000-00-00 00:00:00') ? ';color:#ccc;font-style:italic' : '').(($time_xtra) ? ';visibility:hidden' : '')."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\" onkeyup=\"DFchangeField(this, event)\" /></td>".
                '<td'.(($time_xtra) ? ' '.$time_xtra : '').'>&nbsp;:&nbsp;</td>'.
                '<td><input '.(($obligatory) ? ' obligatory="yes"' : '').' type="text" name="'.$var.'_i'.$i.'" maxlength="2" value="'.(($date['i'] && $valor != '0000-00-00 00:00:00') ? $date['i'] : $l_min).'" '.$readonly.' helpvalue="'.$l_min.'" style="width:3em'.((!$date['i'] || $valor == '0000-00-00 00:00:00') ? ';color:#ccc;font-style:italic' : '').(($time_xtra) ? ';visibility:hidden' : '')."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\" /></td>".
                (($s) ? '<td'.(($time_xtra) ? ' '.$time_xtra : '').'>&nbsp;:&nbsp;</td>' : '').
                (($s) ? '<td><input '.(($obligatory) ? ' obligatory="yes"' : '').' type="text" name="'.$var.'_s'.$i.'" value="'.$date[s].'" style="color:#ccc;width:20px"></td>' : '').
            '</tr>'.
        '</table>';
    } else {
        $day = '<td>'.jp7_app_createSelect($var.'_d'.$i, $l_dia, '---', 1, 31, $date['d'], $readonly.(($obligatory) ? ' obligatory="yes"' : '')).'</td>'.
                '<td>&nbsp;/&nbsp;</td>';
        $month = '<td>'.jp7_app_createSelect($var.'_m'.$i, $l_mes, '---', 1, 12, $date['m'], $readonly.(($obligatory) ? ' obligatory="yes"' : '')).'</td>'.
                '<td>&nbsp;/&nbsp;</td>';

        return ''.
        '<table border="0" cellspacing="0" cellpadding="0">'.
            '<tr>'.
                (($lang->lang == 'en') ? $month.$day : $day.$month).
                '<td>'.jp7_app_createSelect($var.'_Y'.$i, $l_ano, '---', date('Y') - 100, date('Y') + 20, $date['Y'], $readonly.(($obligatory) ? ' obligatory="yes"' : '')).'</td>'.
                '<td'.(($time_xtra) ? ' '.$time_xtra : '').' nowrap>&nbsp;-&nbsp;</td>'.
                '<td>'.jp7_app_createSelect($var.'_H'.$i, 'H', '---', 0, 23, $date['H'], $time_xtra).'</td>'.
                '<td'.(($time_xtra) ? ' '.$time_xtra : '').'>&nbsp;:&nbsp;</td>'.
                '<td>'.jp7_app_createSelect($var.'_i'.$i, 'M', '---', 0, 59, $date['i'], $time_xtra).'</td>'.
                (($s) ? '<td'.(($time_xtra) ? ' '.$time_xtra : '').'>&nbsp;:&nbsp;</td>' : '').
                (($s) ? '<td><input '.(($obligatory) ? ' obligatory="yes"' : '').' type="text" name="'.$var.'_s'.$i.'" value="'.$date['s'].'" style="color:#ccc;width:20px"></td>' : '').
            '</tr>'.
        '</table>';
    }
}

/**
 * Adiciona string ao arquivo de log, que é gravado dentro da pasta $c_interadminConfigPath/_log.
 *
 * @param string $log Prefixo do nome do arquivo de log. Por exemplo: sql
 * @param string $S String a ser adicionada ao arquivo de log.
 */
function jp7_app_log($log, $S)
{
    global $s_user;
    $app_user = $s_user['login'];
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    Log::info('[APP]['.$log.'] '.$s_user['login'].' - '.$ip.' - '.$S);
}

// jp7_msg (2003/XX/XX)
function jp7_msg($S, $type)
{
    include jp7_path_find('inc/msg.php');
}

// jp7_phpmyadmin_path (2004/06/23)
/**
 * @deprecated Não é mais utilizado o phpmyadmin para backup
 */
function jp7_phpmyadmin_path($path = '../_admin/phpmyadmin/', $i = 0)
{
    if (is_dir($path) || $i > 3) {
        return $path;
    } else {
        return jp7_phpmyadmin_path('../'.$path, $i++);
    }
}

// jp7_phpmyadmin_aplicacao_path (2007/07/19)
/**
 * @deprecated Não é mais utilizado o phpmyadmin para backup
 */
function jp7_phpmyadmin_aplicacao_path($path = '../_admin/phpmyadmin/', $path2 = '../../')
{
    if (is_dir($path) || $i > 3) {
        global $SCRIPT_NAME;
        global $jp7_app;

        return $path2.((strpos($SCRIPT_NAME, ($jp7_app == 'intertime' || $jp7_app == 'interaccount' || $jp7_app == 'intersite' || $jp7_app == 'intermail_new') ? 'interadmin' : $jp7_app) === false) ? '../' : (($jp7_app == 'intertime' || $jp7_app == 'interaccount' || $jp7_app == 'intersite' || $jp7_app == 'intermail_new') ? 'interadmin' : $jp7_app).'/');
    } else {
        return jp7_phpmyadmin_aplicacao_path('../'.$path, '../'.$path2);
    }
}

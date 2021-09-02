<?php
/*
 * Make array by eloquent model
 *
 * */
function g_makeArrayIDKey($data, $idField='id')
{
    $result = array();

    foreach ($data as $record) {
        if (!isset($record->$idField)) {
            continue;
        }
        $result[$record->$idField] = $record;
    }

    return $result;
}

/*
 * Make array by array
 *
 * */
function g_makeArrayIDKeyFromArray($data, $idField='id')
{
    $result = array();

    foreach ($data as $record) {
        if (!isset($record[$idField])) {
            continue;
        }
        $result[$record[$idField]] = $record;
    }

    return $result;
}

function g_makeGroupArrayIDKey($data, $idField='id', $secondField = '')
{
    $result = array();

    foreach ($data as $record) {
        if (!isset($record[$idField])) {
            continue;
        }
        if (!empty($secondField)) {
            $result[$record[$idField]][$record[$secondField]] = $record;
        } else {
            $result[$record[$idField]][] = $record;
        }
    }

    return $result;
}

function g_enum($enumID, $value = null)
{
    global $g_masterData;

    $enumArray = array();
    if (isset($g_masterData[$enumID]))
        $enumArray = $g_masterData[$enumID];

    if (!is_array($enumArray))
        $enumArray = array();

    // get result
    $result = array();
    if (null !== $value) {
        if (strpos($value, ',') !== false) {
            $values = explode(',', $value);
        } else {
            $values = array($value);
        }
        foreach ($values as $value) {
            if (isset($enumArray[$value])) {
                $result[] = $enumArray[$value];
            }
        }
        $result = implode(', ', $result);
        return $result;
    }

    return $enumArray;
}

/**
 * Get specified element from object safely
 *
 * @param array $arr
 * @param string $key1
 * @param string $key2
 * @param mixed $default
 * @return mixed
 */
function g_getArrayValue($arr, $key1, $key2 = '', $default = '')
{
    if (empty($key2)) {
        return (isset($arr[$key1]) ? $arr[$key1] : $default);
    }

    return (isset($arr[$key1][$key2]) ? $arr[$key1][$key2] : $default);
}

/**
 * Get specified element from object safely
 * (参照：g_getArrayValue とほぼ同じ)
 *
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function g_getValue($array, $key, $default)
{
    $key = (string) $key;
    if (isset($array[$key])) {
        return $array[$key];
    }
    return $default;
}

/**
 * Format number from text
 * 32432.232 => "32,432.23"
 * 0 => "0.00"
 * @param number $val
 * @param number $precision=2
 * @return string
 * @author KIS
 */
function g_numberFormat($val, $precision=0)
{
    return number_format($val, $precision, '.', ' ');
}

function g_extractField($arr, $key) {
    $ret = [];
    foreach($arr as $record) {
        if(isset($record[$key])) {
            $ret[] = $record[$key];
        }
    }
    return $ret;
}

function cAsset($path)
{
    if (env('APP_ENV') === 'production') {
        return secure_url(ltrim($path, '/'));
    }

    return url(ltrim($path, '/'));
}

function cUrl($path) {
    if (env('APP_ENV') === 'production') {
        return secure_url($path);
    }

    return url($path);
}

function g_generateRandomString($characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length = 8) {
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function g_isMobile() {
    if(isset($_SERVER['HTTP_USER_AGENT'])) {
        $useragent=$_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/(android(?!.*(mobi|opera mini)))/i', strtolower($useragent))) {
            return true ;
        }
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
            return true ;
        }
    }
    return 0 ;
}
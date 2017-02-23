<?php


	function product_discount($price, $discount, $f = false)
	{
	    if ($f) {
	        return $price * ($discount / 100);
	    } else {
	        return $price - ($price * $discount / 100);
	    }
	}

	function  no_admin_exist()
	{
	    try {
	        $conn = dbconnect();
	        $q    = $conn->query("SELECT * FROM " . ADMIN_TBL);
	        $q->execute();
	        $q->setFetchMode(PDO::FETCH_ASSOC);

	        return $q->rowCount() == 0 ? false : true;
	    } catch (PDOException $pe) {
	        echo db_error($pe->getMessage());
	    }
	}

	function clean_link($str)
	{
	    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
	    $clean = strtolower(trim($clean, '-'));
	    $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
	    return $clean;
	}

        //SITE_EMAIL, 'Pending Order', $msg, 1, $_POST['email']
	function send_mail($to, $subject, $msg, $type, $emailfrom, $name = null)
	{
            
            require_once(BASE_PATH.'includes/phpmailer/class.phpmailer.php');
            require_once(BASE_PATH.'includes/phpmailer/class.smtp.php');
            $namefrm = ($name == null) ? ($emailfrom) : ($name);
            $mail = new PHPMailer();
            
            //$mail->SMTPDebug = 3;                               
            $mail->isSMTP();                                     
            $mail->Host = 'gator3087.hostgator.com';  
            $mail->SMTPAuth = true;                               
            $mail->Username = 'no-reply@scarlettsnursery.com';                
            $mail->Password = 'poopietoes94';                           
            $mail->SMTPSecure = 'ssl';                            
            $mail->Port = 465;  
            
            
            
            $mail->SetFrom( $emailfrom , $namefrm );
            $mail->AddReplyTo( $emailfrom , $namefrm );
            $mail->AddAddress( $to , $to );
            $mail->Subject = $subject;
            

            $mail->MsgHTML( $msg );
            $sendEmail = $mail->Send();
            
            print_r($sendEmail);
	    

	    if( $sendEmail == true ):
                return true;
            else:
                return false;
            endif;
	}

	function update_views($conn, $col = 'views',$tbl = PRODUCTS_TBL,$id = 'prodid')
	{
	    try {
	        $qry = "UPDATE " . $tbl . "
	        SET " . $col . "=" . $col . "+1
	        WHERE $id=:id";
	        $q   = $conn->prepare($qry);
	        $q->bindParam(':id', $_GET['p']);
	        $q->execute();
	    } catch (PDOException $pe) {
	        echo db_error($pe->getMessage());
	        exit;
	    }
	}

	function check_auth_admin()
	{
	    if (!isset($_SESSION['valid_admin'])) {
	        goto_location('login');
	        exit;
	    }
	}

	function accordion_panels($arr1, $arr2, $ac)
	{
	    return str_replace($arr1, $arr2, $ac);
	}

	function get_related_products($title, $pid, $conn, $list)
	{
	    $qstr = @explode(" ", $title);
	    $str  = array();
	    $s = count($qstr);

	    $a = array('with','part','less');

	    for ($i = 0; $i < $s; $i++) {
	        (strlen($qstr[$i]) > 2 && !in_array(strtolower($qstr[$i]), $a)) ? (array_push($str, $qstr[$i])) : ('');
	    }
	    $sr   = count($str);
	    $q1   = '';
	    $last = $sr;
	    for ($i = 0; $i < $sr; $i++) {
	        $q1 .= " WHEN prodname LIKE '%" . addslashes($str[$i]) . "%' then $i ";

	    }

	    $fs2 = array();
	    for ($i = 0; $i < $sr; $i++) {
	        $qr = " prodname LIKE '%" . addslashes($str[$i]) . "%'  ";
	        array_push($fs2, $qr);
	    }

	    $s2 = join(" OR ", $fs2);

	    $qry= "
	    SELECT *, date_format(exp_time, '%M %e, %Y %H:%i:%s') as exptime,
	    case
	    $q1
	    else $last end
	    as relevancy
	    FROM " . PRODUCTS_TBL . "
	    WHERE
	    ($s2)
	    AND prodid !='" . $pid . "'
	    AND
	    status='1'
	    ORDER
	    BY relevancy DESC limit 12
	    ";

	    if ($s2 != '') {
	        $q = $conn->query($qry);
	        $q->setFetchMode(PDO::FETCH_ASSOC);
	    }


	    if ($q->rowCount() == 0) {
	        return;
	    } else {
	        if ($s2 != '') {
	            ;
	        }
	        {
	            $i     = 0;
	            $stack = array();

	            $related = $related2= '';
	            while ($rows = $q->fetch()) {
	                $i++;
	                $item = list_products($rows);
	                $related .= '<div class="col-md-3">' . str_replace($item[0], $item[1], $list) . '</div>';
	                if ($i == 4) {
	                    array_push($stack, $related);
	                    $related = $related2= '';
	                    $i       = 0;
	                } else {
	                    $related2 .= '<div class="col-md-3">' . str_replace($item[0], $item[1], $list) . '</div>';
	                }

	            }
	            if ($related2 != '') {
	                array_push($stack, $related2);
	            }
	            $i       = 0;
	            $related = '';
	            if (count($stack > 0)) {
	                foreach ($stack as $k => $v) {
	                    if ($i == 0) {
	                        $related .= '<div class="item active"><div class="row">' . $v . '</div></div>';
	                    } else {
	                        $related .= '<div class="item"><div class="row">' . $v . '</div></div>';
	                    }

	                    $i++;
	                }
	            }
	        }

	        return $related;
	    }


	}

	function create_folder($folder = null)
	{
	    if (!is_null($folder)) {

	        if (!is_dir($folder)) {
	            $old_musk = umask(0);
	            mkdir($folder , 0755);
	            umask($old_musk);
	        }
	    }
	}

	function list_products($row)
	{

	    $replace = array(
	        '{ProdId}',
	        '{ProductImg}',
	        '{Url}',
	        '{ProductName}',
	        '{ProductDesc}',
	        '{CurrencyCode}',
	        '{expTime}',
	    );

	    $img = (!empty($row['img1'])) ? (SITE_URL . '/' . THUMB_IMGS . $row['img1']) : (SITE_URL . JS_FOLDER . 'holder.js/300x180/auto/text:' . NO_IMAGE);

	    $products = array(
	        '{ProdId}'      => $row['prodid'],
	        '{ProductImg}'  => $img,
	        '{Url}'         => SITE_URL . '/pdetails/' . $row['prodid'] . '/' . clean_link($row['prodname']),
	        '{ProductName}' => htmlspecialchars(substr($row['prodname'], 0, 100)),
	        '{ProductDesc}' => htmlspecialchars($row['prodesc']),
	        '{CurrencyCode}'=> CURRENCY_CODE,
	        '{expTime}'     => $row['exptime']
	    );

	    if ($row['timer_stat'] == 0 || $row['exp_time'] == '0000-00-00 00:00:00') {
	        $products['{expTime}'] = '';
	    }

	    if ($row['discount'] == 0) {
	        $replace['{ProductDiscount}'] = '{ProductDiscount}';
	        $products['{ProductDiscount}'] = CURRENCY_CODE . number_format($row['price'], 2);
	        $products['{ProductPrice}'] = '';
	        $replace['{ProductPrice}'] = '{ProductPrice}';
	    } else {
	        $replace['{ProductPrice}'] = '{ProductPrice}';
	        $products['{ProductPrice}'] = CURRENCY_CODE . number_format($row['price'], 2);
	        $price = product_discount($row['price'], $row['discount']);
	        $products['{ProductDiscount}'] = CURRENCY_CODE . number_format($price,2);
	        $replace['{ProductDiscount}'] = '{ProductDiscount}';
	    }


	    return array($replace,$products);

	}

	function upload_name($imgFile)
	{
	    $RandomNum = rand(0, 9999999999);
	    $ImageName = str_replace(' ', '-', strtolower($imgFile["name"]));
	    $exp       = explode('.', $imgFile["name"]);
	    $ImageExt  = array_pop($exp);
	    $ImageName = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
	    return substr($ImageName, 0, 30) . '-' . $RandomNum . '.' . $ImageExt;
	}

	function list_delivery_options($conn)
	{
	    try {

	        $sql = "SELECT * FROM " . DELIVERY_TBL . " ORDER BY amount asc";
	        $q   = $conn->query($sql);
	        $q->setFetchMode(PDO::FETCH_ASSOC);
	        $d   = $o   = '';
	        while ($row = $q->fetch()) {
	            $cod = $row['cod'] == 1 ? (' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Cash on Delivery service"></span>') : ('');
	            $o .= '<option value="' . $row['cod'] . '-' . $row['amount'] . '">' . $row['d_options'] . ' (' . CURRENCY_CODE . $row['amount'] . ')</option>';
	            $d .= '<hr id="rz' . $row['id'] . '"><dt style="text-align: left; line-height:1.6"  id="rx' . $row['id'] . '">' . $row['d_options'] . $cod . '</dt><dd id="ry' . $row['id'] . '">' . CURRENCY_CODE . $row['amount'] . ' &nbsp; &nbsp; <span id="del" data-id="' . $row['id'] . '" class="glyphicon glyphicon-remove" aria-hidden="true"></span> </dd>';
	        }
	    } catch (PDOException $pe) {
	        echo db_error($pe->getMessage());
	    }
	    return array('shp1'=> $o,'shp2'=> '<dl class="dl-horizontal">' . $d . '</dl>');
	}

	function update($table, $fields, $criteria, $pdo = false)
	{

	    foreach ($fields as $k => $value) {
	        if ($value == 'now()' || $pdo) {
	            $updates[] = "{$k}={$value}";
	        } else {
	            $updates[] = "{$k}='{$value}'";
	        }

	    }
	    $update = join(", ", $updates);

	    foreach ($criteria as $col => $value) {
	        $wheres[] = "{$col}='{$value}'";
	    }
	    $where = join(" AND ", $wheres);

	    $string= "UPDATE {$table} SET {$update} WHERE {$where}";
	    return $string;
	}


	function product_attributes($conn, $id, $atid)
	{
	    try {
	        $q = $conn->prepare("SELECT " . ATTRIBUTES_TBL . ".*, " . ATTRIBUTE_VARS_TBL . ".*
	            FROM " . ATTRIBUTES_TBL . "
	            LEFT JOIN
	            " . ATTRIBUTE_VARS_TBL . " ON " . ATTRIBUTES_TBL . ".id=" . ATTRIBUTE_VARS_TBL . ".id
	            WHERE " . ATTRIBUTES_TBL . ".id=:id");
	        $q->bindParam(':id', $id, PDO::PARAM_INT);
	        $q->execute();
	        $q->setFetchMode(PDO::FETCH_ASSOC);
	        $o = null;
	        while ($row = $q->fetch()) {
	            $name = stripslashes($row['name']);
	            $val  = $atid == 2 ? ($row['price']) : ('');
	            $price= ($atid == 2) ? ('&nbsp;&nbsp;(' . $row['price'] . ')') : ('');
	            $o .= '<option id="' . $row['uid'] . '" value="' . $val . '">' . stripslashes($row['var']) . $price . ' </option>';
	        }

	    } catch (PDOException $pe) {
	        echo db_error($pe->getMessage());
	        exit;
	    }
	    return '<div class="form-group">
	    <label><span id="lbl' . $atid . '">' . $name . ' [' . $id . '] &nbsp; &nbsp;</span><small>
	    <a href="#" data-toggle="modal" name="attrib-modal" id="' . $atid . '" data-target="#attrib">Edit</a>  &nbsp; &nbsp;
	    <span id="del_attrib" data-id="' . $atid . '"  data-attrib="attribute' . $atid . '" class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></small></span>
	    </label><select  class="form-control" id="attribute' . $atid . '">' . $o . '
	    </select>
	    <input  id="atr' . $atid . '" name="atr' . $atid . '" type="hidden" value="' . $id . '">
	    </div>';
	}

	function get_cart_attributes($conn, $id, $selname)
	{
	    $q = $conn->prepare("SELECT " . ATTRIBUTES_TBL . ".*, " . ATTRIBUTE_VARS_TBL . ".*
	        FROM " . ATTRIBUTES_TBL . "
	        LEFT JOIN
	        " . ATTRIBUTE_VARS_TBL . " ON " . ATTRIBUTES_TBL . ".id=" . ATTRIBUTE_VARS_TBL . ".id
	        WHERE " . ATTRIBUTES_TBL . ".id=:id");
	    $q->bindParam(':id', $id, PDO::PARAM_INT);
	    $q->execute();
	    $q->setFetchMode(PDO::FETCH_ASSOC);
	    $o = null;
	    while ($row = $q->fetch()) {
	        $name        = stripslashes($row['name']);
	        $stock       = trim($row['stock']);
	        $disable     = $disable_txt = '';
	        if ($stock != '' && $stock < 1) {
	            $disable     = ' disabled="disabled" style="color: rgb(204, 204, 204);" ';
	            $disable_txt = OUT_OF_STOCK;
	        }

	        $price = trim($row['price'] != '' && $selname != 'attrib1') ? ('-price') : ('');
	        $price2= (trim($row['price'] != '') && $row['price'] > 0 && $selname != 'attrib1') ? ('&nbsp;&nbsp;(' . CURRENCY_CODE . ' ' . $row['price'] . ')') : ('');
	        $o .= '<option id="' . $row['uid'] . '" '.$disable.' value="' . $row['price'] . '">' . stripslashes($row['var']) . $price2 . $disable_txt.' </option>';
	    }
	    return '<div class="form-group"><label for="' . $name . '">' . $name . '</label>
	    <select  class="form-control" id="' . $selname . $price . '">
	    <option>..select</option>
	    ' . $o . '
	    </select><input type="hidden"  id="' . $selname . $selname . '" value="' . $name . '" ></div>';
	}

	function basket_where($w = 'AND')
	{
	    $paras[] = "sessid='" . session_id() . "'";
	    if (isset($_SESSION["custid"])) {
	        $paras[] = "custid='" . $_SESSION["custid"] . "'";
	    }
	    if (isset($_COOKIE["cookieid"])) {
	        $paras[] = "cookieid='" . $_COOKIE["cookieid"] . "'";
	    }
	    $where = join(" OR ", $paras);
	    return " $w (" . $where . ")";
	}

	function db_error($err)
	{
	    if (SHOW_ERR) {
	        return $err;
	    } else {
	        return 'Database error has occured. Please try again later';
	    }

	}


	function gen_id($len)
	{
	    $id = md5(uniqid(microtime(), 1)) . getmypid();
	    return $id = substr($id, 0, $len);
	}

	function get_basket_items($conn, $all = false, $one = null, $cart = false)
	{
	    try {
	        $qry = "SELECT
	        b.prodid,
	        b.attrib1,
	        b.attrib2,
	        b.options_id,
	        b.qty,
	        p.prodname,
	        p.discount,
	        p.stocktotal,
	        p.img1,
	        IF(b.pricesrc, p.price, a.price) as price,
	        a.stock
	        FROM
	        " . SHOPPING_BASKET_TBL . " b
	        LEFT JOIN " . PRODUCTS_TBL . " p
	        ON (b.priceid = p.prodid AND b.pricesrc = 1)
	        LEFT JOIN  " . ATTRIBUTE_VARS_TBL . " a
	        ON (b.priceid = a.uid AND b.pricesrc = 0)";
	        $qry .= basket_where('WHERE');
	        $qry .= " ORDER By b.dateadded DESC";
	        $q = $conn->query($qry);
	        $q->setFetchMode(PDO::FETCH_ASSOC);
	    } catch (PDOException $pe) {
	        echo db_error($pe->getMessage());
	    }
	    $info      = $img       = $r         = $pname     = $rw        = $items     = '';
	    $total_amt = $row_total = $discounts = 0;
	    $itemz     = array();
	    $xi = 1;
	    while ($row = $q->fetch()) {
	        array_walk_recursive($row, create_function('&$val', '$val = stripslashes($val);'));
	        $items += $row['qty'];
	        if (!empty($row['prodname'])) {
	            $pname       = $row['prodname'];
	            $stock_total = $row['stocktotal'];
	            $price       = product_discount($row['price'], $row['discount']);
	            $d_amt       = product_discount($row['price'], $row['discount'], 1);
	            $discount_val= $row['discount'];
	            if ($cart) {
	                $row_total = ($price * $row['qty']);
	            }
	            $total_amt += ($price * $row['qty']);
	            $img = $row['img1'];
	        } else {
	            $pinfo        = getItemInfo($conn, $row['prodid']);
	            $pname        = $pinfo[0];
	            $price        = product_discount($row['price'], $pinfo[1]);
	            $d_amt        = product_discount($row['price'], $pinfo[1], 1);
	            $discount_val = $pinfo[1];
	            $stock_total  = $pinfo[3];
	            if ($cart) {
	                $row_total = ($price * $row['qty']);
	            }
	            $total_amt += ($price * $row['qty']);
	            $img = $pinfo[2];
	        }
	        $d    = get_product_info($row);
	        $data = $row['qty'] . ' x ' . $pname . ' / ' . CURRENCY_CODE . $price;
	        $data2= $row['qty'] . ' x ' . $pname;
	        if (!is_null($one) && $row['prodid'] == $one) {
	            $info = $data;
	        }

	        if ($cart) {
	            $c_url = SITE_URL . '/pdetails/' . $row['prodid'] . '/' . clean_link($pname);
	            $thumb = (trim($img) != '') ? ('<img src="product-img.php?img=' . $img . '" class="img-responsive">') : ('');
	            $dv    = ($discount_val > 0) ? ('<br /><span class="text-danger"><small>You save: ' . CURRENCY_CODE . $d_amt . ' (' . $discount_val . '%)</small></span>') : ('');
	            $rw .= '<tr><td  width="15%">' . $thumb . '</td><td class="active" width="30%"><a class="item-title" href="' . $c_url . '">' . $pname . '</a> <span class="text-muted"><small>' . $d . '</small></span></td><td width="10%" ><input type="text" class="form-control" name="updateq[' . $row['prodid'] . ']" value="' . $row['qty'] . '" style="width:60px" maxlength="1"></td><td class="active" width="14%">' . CURRENCY_CODE . number_format($price,
	                2) . $dv . '</td>
	            <td  width="14%" >' . CURRENCY_CODE . number_format($row_total, 2) . '</td><td class="active" width="5%">
	            <a  id="' . $row['prodid'] . '" href="cart?cmd=del&id=' . $row['prodid'] . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td></tr>';
	        }
	        $inventory_arr = array(
	            'st0' =>$stock_total,
	            'st1' =>$row['stock'],
	            'st2' =>$row['stock'],
	            'bst' =>$row['options_id'],
	            'nm'  =>$pname,
	            'attr'=>$d,
	        );
	        if ($all) {
	            $shipping = isset($_POST['delivery']) ? ($_POST['delivery']) : (0);
	            $s_rate = ($xi == 1) ? ($shipping) : (0);
	            $itemz[$xi]['id'] = $xi;
	            $itemz[$xi]['name'] = $pname . ' / ' . CURRENCY_CODE . $price . $d;
	            $itemz[$xi]['qty'] = $row['qty'];
	            $itemz[$xi]['shipping'] = $s_rate;
	            $itemz[$xi]['amount'] = $price;
	            $itemz[$xi]['inv_arr'] = $inventory_arr;
	            $r .= '<li class="list-group-item" id="p' . $row['prodid'] . '">' . $data . '</li>';
	            $info = '<ul class="list-group">' . $r . '</ul>';
	        }
	        $xi++;
	    }
	    $arr = array(
	        'info'     => $info,
	        'pid'      => $one,
	        'plain_tot'=> $total_amt,
	        'total'    => number_format($total_amt, 2),
	        'items'    => $items,
	        'items2'   => $r,
	        'item3'    => $itemz,
	        'currency' => CURRENCY_CODE
	    );
	    if ($cart) {
	        $arr['cart'] = $rw;
	    }
	    return ($all || $cart) ? ($arr) : (json_encode($arr));

	}

	function get_email_address($conn)
	{

	    $q   = $conn->prepare("SELECT email  FROM " . ADMIN_TBL . " where user='" . $_SESSION['valid_admin'] . "'");
	    $q->execute();
	    $q->setFetchMode(PDO::FETCH_ASSOC);
	    $row = $q->fetch();
	    return $row['email'];

	}

	function basket_summary($basket)
	{
	    $itm = ($basket['items'] == 1) ? (' item') : (' items');
	    if ($basket['total'] > 0) {
	        $basket_summary = $basket['items'] . $itm . ' (' . CURRENCY_CODE . $basket['total'] . ')';
	    } else {
	        $basket_summary = CURRENCY_CODE . '0';
	    }

	    return $basket_summary;
	}


	function getItemInfo($conn, $pid)
	{
	    $q   = $conn->prepare("SELECT prodname, discount, img1, stocktotal FROM " . PRODUCTS_TBL . " where prodid=:pid");
	    $q->bindParam(':pid', $pid, PDO::PARAM_INT);
	    $q->execute();
	    $q->setFetchMode(PDO::FETCH_ASSOC);
	    $row = $q->fetch();
	    return array(stripslashes($row['prodname']),$row['discount'],$row['img1'],$row['stocktotal']);
	}

	function insert($table, $fields)
	{
	    foreach ($fields as $k => $v) {
	        $key[] = "{$k}";
	        $value[] = "{$v}";
	    }
	    $keys   = join(", ", $key);
	    $values = join(", ", $value);

	    return "INSERT INTO {$table} ({$keys}) VALUES({$values})";
	}

	function get_stock_status($stock)
	{
	    if ($stock > 0) {
	        $qry = "SELECT * FROM " . CATEGORIES_TBL . " where id=:id";
	        $q   = $conn->prepare($qry);
	        $q->execute();
	        $q->setFetchMode(PDO::FETCH_ASSOC);
	    }

	}

	function save_pages($conn)
	{
	    try {

	        $sql = "SELECT * FROM " . PAGES_TBL ." WHERE page_status=1";
	        $q   = $conn->query($sql);
	        $q->setFetchMode(PDO::FETCH_ASSOC);

	        if ($q->rowCount() == 0 && file_exists('../'.INC_FOLDER . CACHE_FILE .'pages_cache.txt')) {
	            unlink('../'.INC_FOLDER . CACHE_FILE .'pages_cache.txt');
	        }
	        $link = '';
	        while ($rows = $q->fetch()) {
	            $tpl = file_get_contents(ADMIN_HTML. 'pages.tpl');

	            $find= array(
	                '{TITLE}',
	                '{MAIN_TITLE}',
	                '{CONTENT}'
	            );

	            $replace = array(
	                stripslashes($rows['page_name']),
	                stripslashes($rows['main_title']),
	                stripslashes($rows['page_content'])
	            );

	            $page = str_replace($find, $replace, $tpl);
	            cachefile('../'.$rows['page_link'].'.php', $page);
	            $link .= '<li><a href="'.SITE_URL.'/'.$rows['page_link'].'">'.stripslashes($rows['page_name']).'</a></li>';
	        }

	    }
	    catch (PDOException $pe) {

	        echo db_error($pe->getMessage());

	    }
	    if (strlen($link) > 1) cachefile('../'.INC_FOLDER . CACHE_FILE .'pages_cache.txt', $link);
	}

	function get_featured_items($conn)
	{
	    if (file_exists('../'.INC_FOLDER . CACHE_FILE .'fjson.txt')) {
	        $cache = json_decode('../'.INC_FOLDER . CACHE_FILE .'fjson.txt');
	        foreach ($cache as $k =>$v) {
	            if (is_array($v))

	            # $v['title'].' | '.$v['pid'].' | '.$v['img'].' < br > ';
	            $sql = "SELECT *
	            FROM
	            " . IMAGES_TBL . "
	            WHERE
	            prodid='".(int) $pid."'";

	            $q = $conn->query($sql);
	            $q->setFetchMode(PDO::FETCH_ASSOC);
	        }
	    }
	}

	function update_inventory($arr,$conn)
	{
	    reset($arr);
	    $return        = $current_stock = null;
	    foreach ($arr as $v) {
	        $i = explode(":", $v['inv_arr']['bst']);
	        $id= (int) $i[0];
	        $qty = (int) $i[1];
	        $op1 = (int) $i[2];
	        $op2 = (int) $i[3];

	        if ($v['inv_arr']['st0'] > 0) {
	            $qry = "UPDATE   " . PRODUCTS_TBL  ." set stocktotal =stocktotal-$qty WHERE prodid =$id";
	            $q   = $conn->query($qry);
	            if (LOW_STOCK_ALERT && $v['inv_arr']['st0'] <= LOW_STOCK_LEVEL) {
	                $current_stock = $v['inv_arr']['st0'] - $qty;
	                $return .= $v['inv_arr']['nm'].'<br />Current Stock: '.$current_stock.'<br/><br/>';
	            }
	        }
	        if ($op1 > 0) {
	            $qry      = "SELECT stock FROM   " . ATTRIBUTE_VARS_TBL ." WHERE uid =$op1";
	            $q        = $conn->query($qry);
	            $q->setFetchMode(PDO::FETCH_ASSOC);
	            $row      = $q->fetch();
	            $op_stock = $row['stock'];
	            $c        = false;

	            if ($op_stock > 0) {
	                $qry = "UPDATE   " . ATTRIBUTE_VARS_TBL ." set stock =stock-$qty WHERE uid =$op1";
	                $q   = $conn->query($qry);
	                $c   = true;
	            }

	            if ($op_stock != '' && LOW_STOCK_ALERT && $op_stock <= LOW_STOCK_LEVEL) {
	                $current_stock = ($c) ? ($op_stock - $qty) : ($op_stock);
	                $return .= $v['inv_arr']['nm'].'<br />'.strip_tags($v['inv_arr']['attr']).'<br />Current Stock: '.$current_stock.'<br/><br/>';
	            }
	        }

	        if ($op2 > 0) {
	            $qry      = "SELECT stock FROM   " . ATTRIBUTE_VARS_TBL ." WHERE uid =$op2";
	            $q        = $conn->query($qry);
	            $q->setFetchMode(PDO::FETCH_ASSOC);
	            $row      = $q->fetch();
	            $op_stock = $row['stock'];
	            $c        = false;

	            if ($op_stock > 0) {
	                $qry = "UPDATE   " . ATTRIBUTE_VARS_TBL ." set stock =stock-$qty WHERE uid =$op2";
	                $q   = $conn->query($qry);
	                $c   = true;
	            }

	            if ($op_stock != '' && LOW_STOCK_ALERT && $op_stock <= LOW_STOCK_LEVEL) {
	                $current_stock = ($c) ? ($op_stock - $qty) : ($op_stock);
	                $return .= $v['inv_arr']['nm'].'<br />'.strip_tags($v['inv_arr']['attr']).'<br />Current Stock: '.$current_stock.'<br/><br/>';
	            }
	        }



	    }

	    return $return;
	}
	function get_product_info($row)
	{
	    if (!is_null($row['attrib1']) || !is_null($row['attrib2'])) {
	        $arr = array_merge((array)$row['attrib1'], (array)$row['attrib2']);
	        $a = '<br />' . join("<br /> ", $arr);
	    } else {
	        $a = '';
	    }
	    return stripslashes($a);
	}

	function goto_location($link)
	{
	    return header("Location: $link");
	}

	function get_categories($conn, $n = null, $c = null, $hc = array())
	{

	    $qry = "SELECT * FROM " . CATEGORIES_TBL . " ORDER By catid desc";
	    $q   = $conn->prepare($qry);
	    $q->execute();
	    $q->setFetchMode(PDO::FETCH_ASSOC);
	    $sl  = $li  = $liMenu = '';

	    while ($row = $q->fetch()) {
	        $cat = (!is_null($c) && $c == $row['catid']) ? (' selected') : ('');
	        $sl .= '<option value="' . $row['catid'] . '" ' . $cat . '>' . stripslashes($row['cat_name']) . '</option>';
	        $li .= '<li class="list-group-item" id="rw' . $row['catid'] . '"><span id="spancatn' . $row['catid'] . '">
	        <input type="text" id="catn' . $row['catid'] . '"  style="width:70%; padding:3px;" name="catn' . $row['catid'] . '" value="' . stripslashes($row['cat_name']) . '">
	        <input type="submit" value="Save" onclick="save_cat(' . $row['catid'] . ')" class="btn btn-info btn-sm"></span> &nbsp; &nbsp; <a href="#" onclick="cat_del(' . $row['catid'] . ')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></li>';
	        $hc[] = array(
	            'id'  => $row['catid'],
	            'name'=> $row['cat_name'],
	            'link'=> clean_link($row['cat_name']).'/1'
	        );
                //http://scarlettsnursery.com/store/category/16/flowers/1
                $liMenu .= '<li><a href="/store/category/'.$row['catid'].'/'.clean_link($row['cat_name']).'/1">'.  stripslashes($row['cat_name']).'</a></li>';
	    }

	    return
	    array(
	        'm'=>
	        '<ul class="list-group">' . $li . '</ul>',
	        's'=> '<select class="form-control" id="category" name="category" title="Select category" required="" ><option value=""></option>' . $sl . '</select>',
	        'o'=> stripslashes($n),
	        'h'=> $hc,
                'liMenu' => $liMenu
	    );
	}

	function get_attrib_names($conn)
	{

	    $q = $conn->query("SELECT * FROM " . ATTRIBUTES_TBL);
	    $q->setFetchMode(PDO::FETCH_ASSOC);
	    $li= '';
	    while ($row = $q->fetch()) {

	        $li .= '<li class="list-group-item" id="rw' . $row['id'] . '"><span id="spanattrn' . $row['id'] . '">
	        ' . $row['id'] . ' <input type="text" id="attrn' . $row['id'] . '"  style="width:35%; padding:3px;" name="attrn' . $row['id'] . '" value="' . stripslashes($row['name']) . '">
	        <input type="submit" value="Save" onclick="save_attrn(' . $row['id'] . ')" class="btn btn-default btn-sm"></span> &nbsp; &nbsp; <a href="#" onclick="options_list(' . $row['id'] . ')" class="btn btn-default btn-sm">List</a> &nbsp; &nbsp; <a href="#" onclick="select_attrib(' . $row['id'] . ')" class="btn btn-default btn-sm">Select</a> &nbsp; &nbsp; <a href="#" onclick="attrn_del(' . $row['id'] . ')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></li>';
	    }

	    return '<ul class="list-group">' . $li . '</ul>';
	}

	function get_attrib_options_list($conn)
	{
	    $q    = $conn->prepare("SELECT " . ATTRIBUTES_TBL . ".*, " . ATTRIBUTE_VARS_TBL . ".*
	        FROM " . ATTRIBUTES_TBL . "
	        LEFT JOIN
	        " . ATTRIBUTE_VARS_TBL . " ON " . ATTRIBUTES_TBL . ".id=" . ATTRIBUTE_VARS_TBL . ".id
	        WHERE " . ATTRIBUTES_TBL . ".id=:id");
	    $q->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
	    $q->execute();
	    $q->setFetchMode(PDO::FETCH_ASSOC);
	    $li   = $f    = $i    = $name = '';
	    $f    = ($_POST['fi'] == 2) ? (true) : (false);
	    $fm = ($f) ? ('<input type="text" id="optn_price"  onkeydown="allow_digits()" style="width:25%; padding:3px;" name="optn_price" placeholder="price">')
	    : ('');
	    while ($row = $q->fetch()) {
	        $name = $row['name'];
	        if (isset($row['uid'])) {
	            $li .= '<li class="list-group-item" id="rw' . $row['uid'] . '">
	            <span id="spanattr_n_o' . $row['uid'] . '">
	            <input type="text" id="o_attrn' . $row['uid'] . '"  style="width:20%; padding:3px;" name="o_attrn' . $row['uid'] . '" value="' . stripslashes($row['var']) . '" placeholder="name">
	            <input type="text" id="o_attrs' . $row['uid'] . '"  onkeydown="allow_digits(' . $row['uid'] . ')"  style="width:20%; padding:3px;" name="o_attrs' . $row['uid'] . '" value="' . $row['stock'] . '" placeholder="stock">
	            ';
	            if ($f) {
	                $li .= ' <input type="text" id="o_attrp' . $row['uid'] . '"  onkeydown="allow_digits(' . $row['uid'] . ')" style="width:25%; padding:3px;" name="o_attrp' . $row['uid'] . '" value="' . $row['price'] . '" placeholder="price"> ';
	            }
	            $li .= ' <input type="submit" value="Save" onclick="save_o_attrn_p(' . $row['uid'] . ')" class="btn btn-default btn-sm">  ';

	            $li .= '</span> &nbsp; &nbsp;  <a href="#" onclick="attrib_o_n_del(' . $row['uid'] . ')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></li>';

	        }

	    }
	    $i .= '<div class="refresh">
	    <ul class="list-group">' . $li . '</ul></div>';
	    $a = '<div class="refresh">
	    <div id="attr_frm_opts">
	    <ul class="list-group">
	    <li class="list-group-item" >
	    <input type="text"  style="width:25%; height:30px;" name="optn_name" id="optn_name" placeholder="label">
	    <input type="text"  style="width:25%; height:30px;" name="optn_stock" id="optn_stock" placeholder="stock">
	    ' . $fm . ' <button type="submit" class="btn btn-info btn-sm" onclick="add_attrib_options()"> Add New</button>
	    <input id="oid" name="oid" type="hidden" value="' . $_POST['id'] . '">
	    </li>

	    </ul></div></div>
	    ';
	    $b = '<div class="refresh"><br /><div id="rlist"><strong>' . $name . ' ['.$_POST['id'].']</strong>' . $i . '</div></div>';
	    return array('a'=> $a,'b'=> $b);
	}


	function create_thumb($sourcefile, $destfile, $fw, $fh, $jpegquality = 97)
	{
	    list($width, $height, $from_type) = getimagesize($sourcefile);
	    switch ($from_type) {
	        case 1:
	        $srcImage = imageCreateFromGif($sourcefile);
	        break;
	        case 2:

	        $srcImage = imageCreateFromJpeg($sourcefile);
	        break;
	        case 3:
	        $srcImage = imageCreateFromPng($sourcefile);
	        break;
	    }
	    $x_ratio = $fw / $width;
	    $y_ratio = $fh / $height;

	    if (($width <= $fw) && ($height <= $fh)) {
	        $tn_width = $width;
	        $tn_height= $height;
	    } else {
	        if (($x_ratio * $height) < $fh) {
	            $tn_height = ceil($x_ratio * $height);
	            $tn_width  = $fw;
	        } else {
	            $tn_width = ceil($y_ratio * $width);
	            $tn_height= $fh;
	        }
	    }

	    $tempImage = imagecreatetruecolor($tn_width, $tn_height);
	    imagecopyresampled($tempImage, $srcImage, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
	    imageJpeg($tempImage, $destfile, $jpegquality);


	}

	function list_carousel($t, $d, $l, $b, $id, $stat, $img)
	{
	    $status = ($stat == 1) ? (' checked') : ('');
	    $i      = (!is_null($img)) ? ('<img src="product-img.php?img=' . $img . '&t=2" border="0">') : ('');
	    return '
	    <div id="cdiv' . $id . '">
	    <h3>' . $t . '</h3>
	    <p class="pull-right">' . $i . '</p>
	    <p>' . $d . '</p>
	    <p>' . $l . '</p>
	    <p>' . $b . '</p>

	    <br />
	    <a href="#" data-toggle="modal" class="btn btn-success" name="carousel-modal" id="' . $id . '" data-target="#editCarousel">Edit</a>
	    <a class="btn btn-warning" id="' . $id . '" href="">Delete</a> &nbsp; &nbsp; <input  id="' . $id . '" name="stat" type="checkbox" data-on-text="Live" data-off-color="warning" data-on-color="success" ' . $status . '>
	    </p>

	    </div>';
	}

	function prod_attib_frm($id)
	{
	    $p = $id == 2 ? ('with price option') : ('');
	    return '<div class="form-group">
	    <label for="attributes' . $id . '">Attributes  <small>' . $p . '</small> &nbsp; &nbsp;<small><a href="#" data-toggle="modal" name="attrib-modal" id="' . $id . '" data-target="#attrib">Edit</a></small></label>
	    <select class="form-control" name="attribute' . $id . '"  id="attribute' . $id . '">
	    <option value=""></option>
	    </select>
	    </div>';
	}

	function cachefile($path, $filename)
	{
	    $fh = fopen($path, "w+");
	    if (fwrite($fh, $filename) === false) {
	        echo "Cannot write to file ($filename)";
	        exit;
	    }
	    fclose($fh);
	}

	function get_product_images($conn, $id)
	{

	    $qry = "SELECT *  FROM " . IMAGES_TBL . "
	    WHERE prodid=:id";
	    $q   = $conn->prepare($qry);
	    $q->bindParam(':id', $id);
	    $q->execute();
	    $img = '';
	    while ($row = $q->fetch()) {
	        $img .= '<div class="col-xs-6 col-md-3"><a rel="productsImages" href="' . SITE_URL . '/' . IMGSRC . $row['img_name'] . '" class="thumbnail swipebox">
	        <img src="../' . THUMB_IMGS . $row['img_name'] . '"></a>
	        <span id="del" data-id="' . $row['id'] . '" data-pid="' . $row['prodid'] . '" data-img_name="' . $row['img_name'] . '" class="glyphicon glyphicon-remove" aria-hidden="true"></span>
	        <a href="#" id="img_upd" data-id="' . $row['prodid'] . '" data-img="' . $row['img_name'] . '">set as main</a><br /></div>';
	    }
	    return '<div class="row">' . $img . '</div>';
	}

	function cache_carousel($conn)
	{
	    $search = array(
	        '{ACTIVE}',
	        '{PAGINATION_ID}',
	        '{PAGINATION_BOTTOM}',
	        '{IMAGE}',
	        '{IMAGE_ALT}',
	        '{HEADLINE_LINK}',
	        '{HEADLINE}',
	        '{BODY}'
	    );

	    $sql      = "SELECT * FROM " . CAROUSEL_TBL . " WHERE stat=1 and p_img!='' ";
	    $q        = $conn->query($sql);
	    $q->setFetchMode(PDO::FETCH_ASSOC);
	    $p_bottom = $p_sides  = $data     = $body     = '';
	    $total    = $q->rowCount();
	    if ($total > 0) {
	        for ($c = 0; $c < $total; $c++) {
	            $a = ($c == 0) ? (' class="active"') : ('');
	            $p_bottom .= '<li data-target="#myCarousel" data-slide-to="' . $c . '" ' . $a . '></li>';
	            if ($c == 1) {
	                $p_sides = CAROUSEL_SIDE_PAGINATION;
	            }
	        }
	        $carousel = file_get_contents('../' .VIEWS_FOLDER. "carouselInc.php");
	        $c        = 1;
	        while ($row = $q->fetch()) {
	            $carousel1 = $carousel;
	            $active    = ($c == 1) ? (' active') : ('');
	            if ($row['p_desc'] != '') {
	                $body = '<p>' . $row['p_desc'] . '</p>';
	            }
	            if ($row['p_btn'] != '') {
	                $body .= '<p><a class="btn btn-primary btn-lg" href="' . $row['p_link'] . '" role="button">' . $row['p_btn'] . '</a></p>';
	            }

	            $products = array(
	                $active,
	                $row['id'],
	                $p_bottom,
	                IMGSRC . '/' . $row['p_img'],
	                $row['p_title'],
	                $row['p_link'],
	                $row['p_title'],
	                $body
	            );

	            $data .= str_replace($search, $products, $carousel1);
	            $c++;

	        }

	        $cache = '<div id="myCarousel" class="carousel slide" data-ride="carousel">
	        <ol class="carousel-indicators">' . $p_bottom . '</ol> <div class="carousel-inner">' . $data . $p_sides . '</div></div>';
	        cachefile('../' . INC_FOLDER . CACHE_FILE . 'carousel.txt', $cache);
	    }
	    if ($total == 0) {
	        if (file_exists('../' . INC_FOLDER . CACHE_FILE . 'carousel.txt')) unlink('../' . INC_FOLDER . CACHE_FILE . 'carousel.txt');
	    }
	}

	function pagination($page, $count, $rpp, $qrylink, $showpages = false, $m_rw = false, $anchor = '')
	{

	    $nav       = $pagelinks = "";
	    if (is_numeric($page)) {
	        $sqlstart = ($page - 1) * $rpp;
	    } else {
	        $sqlstart = 0;
	        $page     = 1;
	    }

	    if ($count >= $rpp && $count > 0) {

	        $pages = $count / $rpp;
	        $pages = ceil($pages);
	        $tpages= $pages;
	        if ($page == $pages) {
	            $to = $pages;
	        } elseif ($page == $pages - 1) {
	            $to = $page + 1;
	        } elseif ($page == $pages - 2) {
	            $to = $page + 2;
	        } elseif ($page == $pages - 3) {
	            $to = $page + 3;
	        } else {
	            $to = $page + 4;
	        }

	        if ($page == 1 || $page == 2 || $page == 3 || $page == 4) {
	            $from = 1;
	        } else {
	            $from = $page - 4;
	        }
	        if ($m_rw) {
	            $link_typ = '';
	        } else {
	            $link_typ = 'page=';
	        }
	        if ($page != 1 && $page > 5) {
	            $pagelinks .= '<li><a href="' . $qrylink . $link_typ . '1' . $anchor . '">1..</a></li>';
	        }
	        for ($i = $from; $i <= $to; $i++) {
	            if ($i != $page) {
	                $pagelinks .= '<li><a  href="' . $qrylink . $link_typ . $i . $anchor . '">' . $i . '</a></li>';
	            } else {
	                $pagelinks .= " <li class=\"active\"><a href=\"#\">$i <span class=\"sr-only\">(current)</span></a></li>";
	                $page_pos = $i;
	            }
	        }
	        if ($page != $tpages && $showpages) {
	            $pagelinks .= '<li><a href="' . $qrylink . $link_typ . $pages . $anchor . '">..' . $tpages . '</a></li>';
	        }

	    }

	    if ($count > 0) {
	        if ($count > $rpp) {
	            $nav = "<ul class=\"pagination\"> $pagelinks </ul>";


	        }
	    }

	    return array('qstart'=> $sqlstart,'nav'   => $nav);#
	}

	function get_blog_latest()
	{
	    return (file_exists(INC_FOLDER . CACHE_FILE .'blog_latest.txt')) ? (file_get_contents(INC_FOLDER . CACHE_FILE .'blog_latest.txt')) : ('');
	}

	function save_latest_blog($conn)
	{
	    try {

	        $sql   = "SELECT cid,title FROM " . CMS_TBL. " WHERE status=1 order by feature desc LIMIT 12";
	        $q     = $conn->query($sql);
	        $q->setFetchMode(PDO::FETCH_ASSOC);
	        $count = $q->rowCount();
	        $list  = '';

	        while ($row = $q->fetch()) {
	            $list .= '<li class="list-group-item"> <a class="list-lnk-title" href="'.SITE_URL.'/view-blog/'.$row['cid'].'/'.clean_link($row['title']).'">'.stripslashes($row['title']).'</a></li>';

	        }
	        if ($count == 0 && file_exists('../'.INC_FOLDER . CACHE_FILE .'blog_latest.txt')) {
	            unlink('../'.INC_FOLDER . CACHE_FILE .'blog_latest.txt');
	        }
	        else {
	            $data = '<ul class="list-group" style="margin-top:1.5em;">
	            <li class="list-group-item"> <h3>Latest</h3></li>'.$list.'</ul>';
	            cachefile('../'.INC_FOLDER . CACHE_FILE .'blog_latest.txt', $data);
	        }
	    } catch (PDOException $pe) {

	        echo db_error($pe->getMessage());

	    }

	}

?>
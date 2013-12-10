<? 
require "db_connect.php";
require "header.php";
?>

<?
if($_POST[varianty] == "")
{
  $varianty = sql_all("SELECT * FROM products_var WHERE product_id = $_GET[id] AND active = 1 ORDER BY price_add ASC");
  
  if(count($varianty) == 0)
  {
    $var = 0;
  }
  else
  {
    //tu doplniť výber variantu
    echo '
    <h1>Warenkorb</h1>
    <p>'.$lang0024.'</p>
    ';
    ?>
    <form method="post" action="add_to_cart.php?id=<? echo $_GET[id]; ?>&back_page=<? echo $_GET[back_page]; ?>&back_cat=<? echo $_GET[back_cat]; ?>&back_str=<? echo $_GET[back_str]; ?>&doplnene_var=1">
    <div class="productoptions">
    <table width="100" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="main"><b><? echo $lang0012; ?>:</b>&nbsp;</td>
        <td>
        	<select name="varianty">
            <?
            foreach($varianty as $data)
            {
              ?>
              <option value="<? echo $data[0]; ?>"><? echo $data[1]; ?>  <? echo $data[2] > 0 ? "+ SFR ".number_format($data[2],2) : "" ?></option>
              <?  
            }
            ?>
        </select>
      </td>
      <td>
        &nbsp;&nbsp;<input type="image" src="./img/german/button_continue.gif" alt="Weiter" title=" Weiter " />
      </td>
      </tr>
    </table> 
    </div>       
    </form>
    <?    
    $var = $varianty[0];
  }
}
else
{
  $var = $_POST[varianty];
}

if(count($varianty) == 0 || $_GET[doplnene_var] == 1)
{
  if(!isset($_COOKIE[cart]) || $_COOKIE[cart] == 0)
  {
    //nenastavené - vytvor a zapíš
    if($logged) mysql_query("INSERT INTO cart_ids VALUES('',$logged)");
    else mysql_query("INSERT INTO cart_ids VALUES('','')");
    $id = mysql_insert_id();
    SetCookie("cart",$id,time()+60*60*24*7*52,"/");
  }
  else
  {
    $id = $_COOKIE[cart];  
  }
  
  $ex = sql("SELECT count(*),id FROM cart WHERE cart_id = '$id' AND product_id = '$_GET[id]' AND product_var_id = '$var'");
  if($ex[0] > 0)
  {
    //iba update produkt uz existuje
    sql("UPDATE cart SET count = count+1 WHERE id = $ex[1]");
  }
  else
  {
    //vkladame produkt nie je 
    $pr = sql("SELECT vlp FROM products WHERE id = '$_GET[id]'");
    mysql_query("INSERT INTO cart VALUES ('','$_GET[id]','$var','1','$id','$pr[0]')");
  }
  
  header("location: $_GET[back_page].php?cat=$_GET[back_cat]&str=$_GET[back_str]");
}
?>

<?
require "footer.php";
?>
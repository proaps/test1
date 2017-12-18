<?php
$nick = $_COOKIE['nick'];
function format_folder_size($size)
{
 if ($size >= 1073741824)
 {
  $size = number_format($size / 1073741824, 2) . ' GB';
 }
    elseif ($size >= 1048576)
    {
        $size = number_format($size / 1048576, 2) . ' MB';
    }
    elseif ($size >= 1024)
    {
        $size = number_format($size / 1024, 2) . ' KB';
    }
    elseif ($size > 1)
    {
        $size = $size . ' bytes';
    }
    elseif ($size == 1)
    {
        $size = $size . ' byte';
    }
    else
    {
        $size = '0 bytes';
    }
 return $size;
}

function get_folder_size($folder_name)
{
 $total_size = 0;
 $file_data = scandir($folder_name);
 foreach($file_data as $file)
 {
  if($file === '.' or $file === '..')
  {
   continue;
  }
  else
  {
   $path = $folder_name . '/' . $file;
   $total_size = $total_size + filesize($path);
  }
 }
 return format_folder_size($total_size);
}

if(isset($_POST["action"]))
{
 if($_POST["action"] == "fetch")
 {
	 
	$kat = "pliki_uzytkownikow/$nick/*";
  $folder = array_filter(glob($kat), 'is_dir');
  $output = '
  <table class="table table-bordered table-striped">
   <tr>
    <th>Nazwa folderu</th>
    <th>Ilość plików</th>
    <th>Rozmiar</th>
    <th>Usuń folder</th>
    <th>Wgraj plik</th>
    <th>Pokaż pliki</th>
   </tr>
   ';
  if(count($folder) > 0)
  {
   foreach($folder as $name)
   {
	$nazwa= basename($name);
    $output .= '
     <tr>
      <td>'.$nazwa.'</td>
      <td>'.(count(scandir($name)) - 2).'</td>
      <td>'.get_folder_size($name).'</td>
      <td><button type="button" name="delete" data-name="'.$name.'" class="delete btn btn-danger btn-xs">Usuń</button></td>
      <td><button type="button" name="upload" data-name="'.$name.'" class="upload btn btn-info btn-xs">Wgraj plik</button></td>
      <td><button type="button" name="view_files" data-name="'.$name.'" class="view_files btn btn-default btn-xs">Wyświetl pliki</button></td>
     </tr>';
   }
  }
  else
  {
   $output .= '
    <tr>
     <td colspan="6">Nie znaleziono folderu </td>
    </tr>
   ';
  }
  $output .= '</table>';
  echo $output;
 }
 
 if($_POST["action"] == "create")
 {
  if(!file_exists($_SERVER['DOCUMENT_ROOT']."/Lab7/pliki_uzytkownikow/$nick/".$_POST['folder_name'])) 
  {
   mkdir($_SERVER['DOCUMENT_ROOT']."/Lab7/pliki_uzytkownikow/$nick/".$_POST['folder_name'], 0777, true);
   echo 'Utworzono folder';
  }
  else
  {
   echo 'Folder już istnieje';
  }
 }
 
 
 if($_POST["action"] == "delete")
 {
  $files = scandir($_POST["folder_name"]);
  foreach($files as $file)
  {
   if($file === '.' or $file === '..')
   {
    continue;
   }
   else
   {
    unlink($_POST["folder_name"] . '/' . $file);
   }
  }
  if(rmdir($_POST["folder_name"]))
  {
   echo 'Folder usunięto';
  }
 }
 
 if($_POST["action"] == "fetch_files")
 {
  $file_data = scandir($_POST['folder_name']);
  $output = '
  <table class="table table-bordered table-striped">
   <tr>
    <th>Obraz</th>
    <th>Nazwa pliku</th>
	<th>Pobierz plik</th>
    <th>Usuń</th>
   </tr>
  ';
  
  foreach($file_data as $file)
  {
   if($file === '.' or $file === '..')
   {
    continue;
   }
   else
   {
    $path = $_POST["folder_name"] . '/' . $file;
	setcookie("path", $path, time()+60);
    $output .= '
    <tr>
     <td><img src="'.$path.'" class="img-thumbnail" height="50" width="50" /></td>
     <td contenteditable="true" data-folder_name=" '.$_POST["folder_name"].'"  data-file_name = "'.$file.'" class="change_file_name">'.$file.'</td>
	 <td><a href="pobierz.php?file=' . urlencode($file) . '">Pobierz</a></td>
     <td><button name="remove_file" class="remove_file btn btn-danger btn-xs" id="'.$path.'">Usuń</button></td>
    </tr>
    ';
   }
  }
  $output .='</table>';
  echo $output;
 }
 
 if($_POST["action"] == "remove_file")
 {
  if(file_exists($_POST["path"]))
  {
   unlink($_POST["path"]);
   echo 'Usunięto plik';
  }
 }
 
}
?>
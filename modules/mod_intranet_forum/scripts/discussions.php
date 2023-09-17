<?php
if(!is_object($libGlobal) || !$libAuth->isLoggedin())
	exit();

echo '<h1>Diskussionsforum</h1>';

$cmd = 'SELECT * FROM mod_forum_thread ORDER BY created DESC';
$result = $libDb->query($cmd);

echo '<p><a href="index.php?pid=intranet_forum_comment">Eine neue Diskussion anlegen</a></p>';

echo '<table style="width:100%">';
while($row = mysql_fetch_array($result)){
	
	$cmd2 = sprintf('SELECT * FROM mod_forum_comment WHERE thread_id = %s ORDER BY created DESC LIMIT 0,1',
		$libDb->secInp($row['id']));
	$row2 = $libDb->queryArray($cmd2);
	
	echo '<tr>';
	echo '<td style="width:15%">' . $libTime->formatDateTimeString($row['created'], 2) . '</td>';
	echo '<td style="width:70%">';
	
	echo '<a href="index.php?pid=intranet_forum_thread&amp;id=' .$row['id']. '">' . $row['title'] . '</a><br /><br />';
	echo '<a href="index.php?pid=intranet_forum_thread&amp;id=' .$row['id']. '#' .$row2['id']. '">';
	echo $libString->truncate(trim($row2['text']), 100);
	echo '</a>';
	
	echo '</td>';

	echo '<td style="width:15%">' .$libMitglied->getMitgliedSignature($row2['person_id'], "right"). '</td>';
	echo '</tr>';
}
echo '</table>';
?>
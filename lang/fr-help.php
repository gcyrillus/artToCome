<?php if(!defined('PLX_ROOT')) exit; ?>



<h2>Aide</h2>
<p>Fichier d'aide du plugin ArtToCome</p>

<p>&nbsp;</p>
<h3>Installation</h3>
<p>Telecharger et dezipper le plugins dans le repertoireplugins de votre PluXml</p>
<p>Dans l'administration, activer le plugin</p>
<p>&nbsp;</p>

<h3>Utilisation</h3>
<h4>Affichage automatique à un emplacement fixe</h4>
<p>
	Editez le template du thème ou vous voulez voir la liste apparaitre, par exemple le fichier sidebar.php </p>
	<p>Ajoutez y le code suivant &agrave; l'endroit où vous souhaitez voir apparaitre la
	galerie:</p>
<pre>
	<div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
	&lt;?php eval($plxShow->callHook('plxShowNextArtList')) ?&gt;
	</div>
</pre>


<h2>Aide</h2>
<p>  ArtToCome plugin Hrlp file</p>

<p>&nbsp;</p>
<h3>Install</h3>
<p>Download and unzipped plugin in the plugins repertory of Pluxml</p>
<p>From backend administration, activate the plugin.</p>
<p>&nbsp;</p>

<h3>Use</h3>
<h4>print list of next articles</h4>
<p>
	Edit the template file of your Theme wherever you want this list to appear, for instance, sidebar.php </p>
	<p>Add the following code where you want the list to be printed at screen.</p>
<pre>
	<div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
	&lt;?php eval($plxShow->callHook('plxShowNextArtList')) ?&gt;
	</div>
</pre>
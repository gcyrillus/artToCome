<?php
    if(!defined('PLX_ROOT')) {
        die('Oups!');
    }

    class artToCome extends plxPlugin {
		
		const HOOKS = array(
			'plxShowNextArtList',
        );
        const BEGIN_CODE = '<?php' . PHP_EOL;
        const END_CODE = PHP_EOL . '?>';
		
        public function __construct($default_lang) {
            # appel du constructeur de la classe plxPlugin (obligatoire)
            parent::__construct($default_lang);

            # Ajoute des hooks
            foreach(self::HOOKS as $hook) {
                $this->addHook($hook, $hook);
            }
			
			

			
        }
		
 public function plxShowNextArtList() {
			
            echo self::BEGIN_CODE;
?>		
		$format = '<li>#art_title</li>';
		$max = 5;
		$cat_id = '';
		$ending = '';
		$sort = 'rsort';
        $capture = '';
		$all='';
        # Génération de notre motif
        $all = (isset($all) ? $all : empty($cat_id)); # pour le hook : si $all = TRUE, n'y passe pas
        $cats = $plxShow->plxMotor->activeCats . '|home'; # toutes les categories active
        if (!$all) {
            if (is_numeric($cat_id)) # inclusion à partir de l'id de la categorie
                $cats = str_pad($cat_id, 3, '0', STR_PAD_LEFT);
            else { # inclusion à partir de url de la categorie
                $cat_id .= '|';
                foreach ($plxShow->plxMotor->aCats as $key => $value) {
                    if (strpos($cat_id, $value['url'] . '|') !== false) {
                        $cats = explode('|', $cat_id);
                        if (in_array($value['url'], $cats)) {
                            $cat_id = str_replace($value['url'] . '|', $key . '|', $cat_id);
                        }
                    }
                }
                $cat_id = substr($cat_id, 0, -1);
                if (empty($cat_id)) {
                    $all = true;
                } else {
                    $cats = $cat_id;
                }
            }
        }
        if (empty($motif)) {# pour le hook. motif par defaut s'il n'a point créé cette variable
            if ($all)
                $motif = '/^[0-9]{4}.(?:[0-9]|home|,)*(?:' . $cats . ')(?:[0-9]|home|,)*.[0-9]{3}.[0-9]{12}.[a-z0-9-]+.xml$/';
            else
                $motif = '/^[0-9]{4}.((?:[0-9]|home|,)*(?:' . $cats . ')(?:[0-9]|home|,)*).[0-9]{3}.[0-9]{12}.[a-z0-9-]+.xml$/';
        }

        # Nouvel objet plxGlob et récupération des fichiers
        $plxGlob_arts = clone $plxShow->plxMotor->plxGlob_arts;
        if ($aFiles = $plxGlob_arts->query($motif, 'art', $sort, 0, $max, 'after')) {
            foreach ($aFiles as $v) { # On parcourt tous les fichiers
                $art = $plxShow->plxMotor->parseArticle(PLX_ROOT . $plxShow->plxMotor->aConf['racine_articles'] . $v);
                $num = intval($art['numero']);
                $date = $art['date'];
                if (($plxShow->plxMotor->mode == 'article') and ($art['numero'] == $plxShow->plxMotor->cible))
                    $status = 'active';
                else
                    $status = 'noactive';

                # Mise en forme de la liste des catégories
                $catList = array();
                $catIds = explode(',', $art['categorie']);
                foreach ($catIds as $idx => $catId) {
                    if (isset($plxShow->plxMotor->aCats[$catId])) { # La catégorie existe
                        $catName = plxUtils::strCheck($plxShow->plxMotor->aCats[$catId]['name']);
                        $catUrl = $plxShow->plxMotor->aCats[$catId]['url'];
                        $catList[] = '<a title="' . $catName . '" href="' . $plxShow->plxMotor->urlRewrite('?categorie' . intval($catId) . '/' . $catUrl) . '">' . $catName . '</a>';
                    } else {
                        $catList[] = L_UNCLASSIFIED;
                    }
                }

                # On modifie nos motifs
                $row = str_replace('#art_id', $num, $format);
                $row = str_replace('#cat_list', implode(', ', $catList), $row);
                $row = str_replace('#art_url', $plxShow->plxMotor->urlRewrite('?article' . $num . '/' . $art['url']), $row);
                $row = str_replace('#art_status', $status, $row);
                $author = plxUtils::getValue($plxShow->plxMotor->aUsers[$art['author']]['name']);
                $row = str_replace('#art_author', plxUtils::strCheck($author), $row);
                $row = str_replace('#art_title', plxUtils::strCheck($art['title']), $row);
                $strlength = preg_match('/#art_chapo\(([0-9]+)\)/', $row, $capture) ? $capture[1] : '100';
                $chapo = plxUtils::truncate($art['chapo'], $strlength, $ending, true, true);
                $row = str_replace('#art_chapo(' . $strlength . ')', '#art_chapo', $row);
                $row = str_replace('#art_chapo', $chapo, $row);
                $strlength = preg_match('/#art_content\(([0-9]+)\)/', $row, $capture) ? $capture[1] : '100';
                $content = plxUtils::truncate($art['content'], $strlength, $ending, true, true);
                $row = str_replace('#art_content(' . $strlength . ')', '#art_content', $row);
                $row = str_replace('#art_content', $content, $row);
                $row = str_replace('#art_date', plxDate::formatDate($date, '#num_day/#num_month/#num_year(4)'), $row);
                $row = str_replace('#art_hour', plxDate::formatDate($date, '#hour:#minute'), $row);
                $row = str_replace('#art_time', plxDate::formatDate($date, '#time'), $row);
                $row = plxDate::formatDate($date, $row);
                $row = str_replace('#art_nbcoms', $art['nb_com'], $row);
                $row = str_replace('#art_thumbnail', '<img class="art_thumbnail" src="#img_url" alt="#img_alt" title="#img_title" />', $row);
                $row = str_replace('#img_url', $plxShow->plxMotor->urlRewrite($art['thumbnail']), $row);
                $row = str_replace('#img_title', $art['thumbnail_title'], $row);
                $row = str_replace('#img_alt', $art['thumbnail_alt'], $row);

                # Hook plugin
                eval($plxShow->plxMotor->plxPlugins->callHook('plxShowLastArtListContent'));

                # On genère notre ligne
                echo '<ul>'.$row.'</ul>';
            }
        }
 
<?php
            echo self::END_CODE;						
        }		
		
		
		
	}
	?>
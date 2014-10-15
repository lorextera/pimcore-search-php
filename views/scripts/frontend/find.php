

<link rel="stylesheet" type="text/css" href="/plugins/SearchPhp/static/css/frontend.css"/>

<? if(!$this->omitSearchForm){ ?>

	<? if(!$this->omitJsIncludes){?>

		<script src="/plugins/SearchPhp/static/js/frontend/jquery-1.3.2.min.js"></script>
		<link rel="stylesheet" href="/plugins/SearchPhp/static/css/jquery-autocomplete.css" type="text/css" />
		<script type="text/javascript" src="/plugins/SearchPhp/static/js/frontend/jquery.autocomplete.js"></script>


	<? } ?>
	<div class="searchForm">
		<form method="post" action="?search=true" id="searchForm">
			<input type="text" value="<? echo  $this->query ?>" name="query" id="query" />

			<? if(is_array($this->availableCategories) and count($this->availableCategories)>0){?>

				<select id="searchCat" name="cat">
					<option value=""><? echo  $this->translate('search_all_categories')?></option>
					<? foreach($this->availableCategories as $category){?>
						<option <? if($this->category==$category){ ?>selected="selected"<? } ?> value="<? echo  $category ?>">
						<? echo  $this->translate('search_category_'.$category)?>
					</option>
					<? } ?>
				</select>
			<? } ?>
			<span class="submit_wrapper"><input class="submit" type="submit" value="<? echo  $this->translate('search_submit')?>"/></span>

			<script type="text/javascript">

			$("#query").autocomplete('/plugin/SearchPhp/frontend/autocomplete/',{
				minChars:3,
				cacheLength: 0,
				extraParams: {
					cat: function() { return $("#searchCat").val(); }
				}
			});
			</script>
		</form>
	</div>
<? } ?>
<? if(!$this->isFormOnly) : ?>
		<div id="search_info">
	<?
	if(count($this->searchResults)>=1)
	{
		$start = $this->perPage*($this->page-1);
		$end = $start + $this->perPage;
		if($end>$this->total)
		{
			$end = $this->total;
		}
	?>
		<?=$this->translate('search_showing_results')?> <? echo  $start+1 ?> - <? echo  $end ?> <? echo  $this->translate('search_results_of')?> <? echo  $this->total ?><br/>
	<?
	}
	else
	{
		echo $this->translate('no_search_results_found');
	}
	?>

	</div>

	<? if(!empty($this->suggestions))
	{ ?>
		<?=$this->translate('search_suggestions') ?>
		<? for($i=0;$i<5;$i++ ) { ?>
			<?  $suggestion = $this->suggestions[$i]; ?>
			<a href="?cat=<? echo  $this->category ?>&query=<? echo  $suggestion ?>"><? echo  $suggestion ?></a>&nbsp;
		<? } ?>

		<? if(count($this->suggestions)>5) { ?>
			<span id="search_result_additional_suggestions" style="display:none;">
			<? for($i=5;$i<count($this->suggestions);$i++ ) : ?>
				<?  $suggestion = $this->suggestions[$i]; ?>
				<a href="?cat=<? echo  $this->category ?>&query=<? echo  $suggestion ?>"><? echo  $suggestion ?></a>&nbsp;
			<? endfor; ?>
			</span>
			<a style="cursor:pointer;" id="search_result_additional_suggestions_hint" onclick="$('search_result_additional_suggestions_hint').style.display='none';$('search_result_additional_suggestions').style.display=''"><? echo  (count($this->suggestions)-5).' '.$this->translate('more_search_suggestions')?></a>
		<? }?>
	<? } ?>

		<? $counter = 1;?>


	<? /* --------- Display grouped by category --------------*/ ?>
	<? if($this->groupByCategory) { ?>
	<?
		$categories = array("nocat");
		foreach($this->searchResults as $searchResult) {
			if(is_array($searchResult['categories'])) {
				foreach($searchResult['categories'] as $cat){
					if(!in_array($cat,$categories)){
						$categories[] = $cat;
					}
					$categorizedSearchResults[$cat][]=$searchResult;
				}
			} else {
				$categorizedSearchResults["nocat"][]=$searchResult;
			}
		}

		if(is_array($categorizedSearchResults)){
			if(is_array($this->categoryOrder) and count($this->categoryOrder)>0){
				$tmp=array();
				foreach($this->categoryOrder as $cat){
					if(!empty($categorizedSearchResults[$cat])){
						$tmp[$cat]=$categorizedSearchResults[$cat];
						unset($categorizedSearchResults[$cat]);
					}
				}
				$categorizedSearchResults=array_merge($tmp,$categorizedSearchResults);
			} else {
				natsort($categorizedSearchResults);
			}
		}

		?>

		<? if(is_array($categorizedSearchResults))
		{ ?>
			<? foreach($categorizedSearchResults as $key=>$categoryResults)
			{ ?>
				<div class="search_result_category category_<? echo  $key ?>">
					<div class="search_category_headline">
						<h2><? echo  $this->translate("search_category_".$key) ?></h2>
					</div>
					<? foreach($categoryResults as $searchResult){ ?>
						<div class="search_result <? if(is_array($searchResult['categories'])) { echo implode(" ",$searchResult['categories']);} ?>">
							<a href="<? echo  $searchResult['url']?>"><? if(!empty($searchResult['title']) and trim($searchResult['title'])!="") { echo trim($searchResult['title']); } else { echo $searchResult['url']; }?></a><br/>
							<? if($searchResult['h1']){?>
								<strong><? echo  $searchResult['h1'] ?></strong>
							<? } ?>
							<div id="resultSumary_<? echo  $counter ?>">
								... <? echo  $searchResult['sumary']?> ...
							</div>

							<? $counter++;?>
						</div>

					<? } ?>

				</div>
			<? } ?>
		<? } ?>
		<? /* --------- /Display grouped by category --------------*/ ?>

		<? /* --------- Display not grouped --------------*/ ?>
	<? } else { ?>

		<? foreach($this->searchResults as $searchResult) { ?>

			<div class="search_result <? if(is_array($searchResult['categories'])) { echo implode(" ",$searchResult['categories']);} ?>">
				<a href="<? echo  $searchResult['url']?>"><? if(!empty($searchResult['title']) and trim($searchResult['title'])!="") { echo trim($searchResult['title']); } else { echo $searchResult['url']; }?></a><br/>
				<? if($searchResult['h1']){?>
					<strong><? echo  $searchResult['h1'] ?></strong>
				<? } ?>
				<div id="resultSumary_<? echo  $counter ?>">
					... <? echo  $searchResult['sumary']?> ...
				</div>
				<? $counter++;?>
			</div>
		<? } ?>

		<? /* --------- /Display not grouped --------------*/ ?>

	<? } ?>

	<? if(count($this->searchResults)>0)
	{ ?>
		<div id="search_paging">
			<?
				if($this->page>3)
				{
					$pageStart = $this->page-2;
				}
				else
					$pageStart=1;

				$pageEnd = $pageStart+5;

				if($pageEnd>$this->pages)
				{
					$pageEnd = $this->pages;
				}
			?>

			<? if($this->pages>0) { ?>
				<? echo  $this->translate("page") ?>
			<? } ?>
			<? if($this->page>1) {?>
				<a href="?query=<? echo  $this->query?>&cat=<? echo  $this->category ?>&page=<? echo  $this->page-1 ?>">&lt;</a>
			<? } ?>
			<? for($i=$pageStart;$i<=$pageEnd;$i++) { ?>
				<a <? if($this->page == $i) { ?>class="active"<? } ?> href="?query=<? echo  $this->query?>&cat=<? echo  $this->category ?>&page=<? echo  $i ?>"><? echo  $i ?></a>
			<? } ?>

			<? if($this->pages > $this->page) { ?>
				<a href="?query=<? echo  $this->query?>&cat=<? echo  $this->category ?>&page=<? echo  $this->page+1 ?>">&gt;</a>
			<? } ?>
		</div>
	<? } ?>
<? endif; ?>
{*<!-- {[The file is published on the basis of YetiForce Public License 3.0 that can be found in the following directory: licenses/LicenseEN.txt or yetiforce.com]} -->*}
{strip}
	<div class="tpl-Vtiger-dashboards-MiniListFooter widgetFooterContent">
		<div class="row no-margin">
			{if $OWNER eq false}
				{assign var="MINILIST_WIDGET_RECORDS" value=[]}
			{else}
				{assign var="MINILIST_WIDGET_RECORDS" value=$MINILIST_WIDGET_MODEL->getRecords($OWNER)}
			{/if}
			<div class="col-md-4">
				<button class="btn btn-sm btn-light recordCount" data-url="{\App\Purifier::encodeHtml($MINILIST_WIDGET_MODEL->getTotalCountURL($OWNER))}">
					<span class="fas fa-signal showRecordCount" title="{\App\Language::translate('LBL_WIDGET_FILTER_TOTAL_COUNT_INFO')}"></span>
					<a class="float-left d-none" href="{\App\Purifier::encodeHtml($MINILIST_WIDGET_MODEL->getListViewURL($OWNER))}"><span class="count badge badge-danger float-left"></span></a>
				</button>
			</div>
			{if count($MINILIST_WIDGET_RECORDS) >= $MINILIST_WIDGET_MODEL->getRecordLimit()}
				<div class="col-md-8">
					<a class="btn btn-sm btn-primary float-right" href="{\App\Purifier::encodeHtml($MINILIST_WIDGET_MODEL->getListViewURL($OWNER))}">
						<span class="fas fa-align-justify"></span>&nbsp;&nbsp;
						{\App\Language::translate('LBL_MORE')}
					</a>
				</div>
			{else}&nbsp;{/if}
		</div>
	</div>
{/strip}

Ext.getCmp('ks_sitemgr_tabs').add({
	title:'###LANG.title###',
	html:'',
	id:'helpTab',
	tbar:[{
		text:'TYPO3 SBS',
		iconCls:'t3-icon-text-html',
		handler:function() {
			loadHelpTabFrame('http://cms.sn.schule.de/admin/administrative-informationen/grundlagen/');
		}
	},'-',{
		text:'TYPO3 Videos',
		iconCls:'t3-icon-text-html',
		handler:function() {
			loadHelpTabFrame('http://typo3.org/documentation/videos/tutorials-v4-de/');
		}
	},{
		text:'TYPO3 Reference',
		iconCls:'t3-icon-text-html',
		handler:function() {
			loadHelpTabFrame('http://typo3.org/documentation/videos/quick-reference-v4-de/');
		}
	},{
		text:'TYPO3 Wiki',
		iconCls:'t3-icon-text-html',
		handler:function() {
			loadHelpTabFrame('http://wiki.typo3.org/Main_Page');
		}
	}]
});

function loadHelpTabFrame(url) {
	buffer = '<iframe width="100%" height="100%" frameborder="0" src="'+url+'">';
	Ext.getCmp('helpTab').update(buffer);
}
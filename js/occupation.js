var IndData = function(data){
	var me = this;
	me.cats = data.inds; 
	me.setData = function(data){
		cats = data.inds;
	};
	me.refresh = function(){
		var tdCat = $("[data-ref='indcat1']");
		for( var ind in me.cats.indcat1){
			var indEnt = me.cats.indcat1[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='indcat2']");
		for( var ind in me.cats.indcat2){
			var indEnt = me.cats.indcat2[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='indcat3']");
		for( var ind in me.cats.indcat3){
			var indEnt = me.cats.indcat3[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='indcat4']");
		for( var ind in me.cats.indcat4){
			var indEnt = me.cats.indcat4[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='indcat5']");
		for( var ind in me.cats.indcat5){
			var indEnt = me.cats.indcat5[ind];
			var link = "<a data-link='setInd' data-ref='" + indEnt.id + "'>" + indEnt.name + "</a><br />";
			tdCat.append(link);
		}
	};
};

var JobCatData = function(data){
	var me = this;
	me.cats = data.jobs; 
	me.setData = function(data){
		cats = data.jobs;
	};
	me.refresh = function(){
		var tdCat = $("[data-ref='jobcat1']");
		for( var no in me.cats.jobcat1){
			var jobEnt = me.cats.jobcat1[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='jobcat2']");
		for( var no in me.cats.jobcat2){
			var jobEnt = me.cats.jobcat2[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='jobcat3']");
		for( var no in me.cats.jobcat3){
			var jobEnt = me.cats.jobcat3[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='jobcat4']");
		for( var no in me.cats.jobcat4){
			var jobEnt = me.cats.jobcat4[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
		tdCat = $("[data-ref='jobcat5']");
		for( var no in me.cats.jobcat5){
			var jobEnt = me.cats.jobcat5[no];
			var link = "<a data-link='setJob' data-ref='" + jobEnt.id + "'>" + jobEnt.name + "</a><br />";
			tdCat.append(link);
		}
	};
};

var clubListTable = function(clubList,columnNum, $position){
	var clubCount =0;
	for (propery in clubList){
		if(clubList.hasOwnProperty(propery))
			clubCount++;
	}
	
	var rowNum = parseInt( clubCount / columnNum);
	var output = '<table class="table table-striped table-bordered">';
	output += '<tbody>';
	
	var iCount = 0;
	for (var key in clubList){
		if (iCount % rowNum == 0){
			output += '<td>';
		}
		var strTemp = '<a data-link="queryClub" data-ref="'+ key + '">'+ clubList[key]  +'</a>';
		output +=strTemp;
		if (iCount % rowNum == rowNum-1){
			output +='</td>';
		}
		else{
			output +='<br />';
		}
		iCount++;
	}
	
	output += '</tbody></table>';
	$position.append(output);
};
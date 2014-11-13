
function jobClub(data){
	var me = this;
	me.club_id = data.club_id;
	me.done = data.done;
	me.showoff = data.showoff;
	
	me.getData = function(){
		var data = {};
		data.club_id = me.club_id;
		data.done = me.done;
		data.showoff = me.showoff;
		return data;
	};
	
	
}
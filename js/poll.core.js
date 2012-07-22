function loadpoll()
	{
		$("#loading_poll").html("Loading...");
		$("#loading_poll").fadeIn("fast");
		$("#poll_container").fadeIn("slow", function () {

		$.post("/poll.core.php", {action:"load"}, function (r){ $("#poll_container").html(r);

			if($("#results").hasClass("results"))
			{
				$("div[id='poll_result']").each(function(){
					var percentage = $(this).attr("name");

					$(this).css({width: "0%"}).animate({
					width: percentage+"%"}, 1600);

					});
			 $("#loading").fadeOut("fast");
			}

		},"html" );});
	}
	function vote()
	{
		var pollId = $("#pollId").val();
		var choice = $("#choice").val();
		$("#poll_container").empty();
		$("#poll_container").append("<div id=\"loading_poll\" style=\"display:none\"><\/div>");
		$("#loading_poll").fadeIn("fast", function () {$("#loading_poll").html("Please wait while your vote is stored...");});

			$.post("/poll.core.php",{action:"vote",pollId:pollId,choice:choice}, function(r)
			{
				if(r.status == 0 )
				$("#loading_poll").fadeIn("fast", function () {$("#loading_poll").empty(); $("#loading_poll").html(r.msg);});
				else if(r.status == 1 )
				{
				$("#loading_poll").empty();
				loadpoll();
				}
			},"json");


	}
	function addvote(val)
	{
		$("#choice").val(val);
		$("#vote_b").show("fast");
	}
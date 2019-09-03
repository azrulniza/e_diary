 $(function () {

            function generateGradient(red, green, blue, count)
            {
                var i;
                var r = red % 256;
                var g = green % 256;
                var b = blue % 256;
                var col = [];
                var step = Math.round(255 / count);
                for (var i = 0; i < count; i++)
                {
                    r += step;
                    g += step;
                    b += step;
                    col[i] = {r: r, g: g, b: b};
                }
                //$("#op").html(str);
                //console.log(col);
                return col;
            }

            // lable all slots with its accompanied HTML5 data
            $(".page-node-slot").each(function () {
			
                var code = $(this).data("slot-code");
                var width = $(this).data("slot-width");
                var height = $(this).data("slot-height");
                
                var thisWidth = $(this).width();
                var thisHeight = $(this).height();
                
                var domSlotInfo = $(this).find(".page-node-slot-content");
                
                var space = thisWidth * thisHeight;
                
                if(space < 55*55){
                    // small space
					
                   domSlotInfo.html(
                        "<div class='info'><p class='slot-code-title'>" + code + "</p>" +
                        "</div>");
                
                   domSlotInfo.addClass("very-small");
                
                }
                else if (space < 160 * 65){
                    
                    domSlotInfo.html(
                                       "<div class='info'><p class='slot-code-title'>" + code + "</p>" +
                                       "<p>" + width + "✕&#x200b;" + height + "px</p></div>"); 
                               
                   domSlotInfo.addClass("very-small");
                }    
                else{
					
                    domSlotInfo.html(
                                       "<div class='info'><p class='slot-code-title'>" + code + "</p>" +
                                       "<p>" + width + "px (W) ✕ " + height + "px (H)</p></div>");               
                   }
                
               
            });

            // colorize each template>slot
            $(".page-node").each(function () {
			
                var slots = $(this).find(".page-node-slot");
                var slotColors = generateGradient(255, 0, 0, slots.length);
                $.each(slots, function (i, domSlot) {
                    var color = slotColors[i];
                    $(domSlot).css("background-color", "rgb(" + color.r + "," + color.g + "," + color.b + ")");
                })
            });
            
            // make row totally clickable
            $("table .table").on("click", "td", function(){
               // console.log("clicked")
                var anchor = $(this).parent().find("a");
                if(anchor.length > 0){
                    window.location = anchor.attr("href");
                }
            });
        });
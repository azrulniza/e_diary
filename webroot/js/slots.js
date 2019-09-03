
  
  
  //$(function () {

    // prevent old plugin error
    $.browser = {msie: false};


    var isSaveRequired = false;

	


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

    var template = getAppVars().template;
    var slots = template.page_slots;

    var sizeVertical = {width: 1080, height: 1920};
    var sizeHorizontal = {width: 1920, height: 1080};

    // calculate scale
    var windowWidth = $("body").width();
    var scaleRatio;



    var domTemplate = $("#template");
    //console.log(slotColors);

    // draw the outer template
    if (template.orientation === 'VERTICAL') {

        if (windowWidth > 768) {
            scaleRatio = windowWidth / 3 / sizeVertical.width;
        } else {
            windowWidth = $("#template").width();
            scaleRatio = windowWidth / sizeVertical.width;
        }


        domTemplate
                .css("width", sizeVertical.width * scaleRatio)
                .css("height", sizeVertical.height * scaleRatio);
    } else {

        if (windowWidth > 768) {
            scaleRatio = windowWidth / 2 / sizeHorizontal.width;

        } else {
            windowWidth = $("#template").width();
            scaleRatio = windowWidth / sizeHorizontal.width;

        }

        domTemplate
                .css("width", sizeHorizontal.width * scaleRatio)
                .css("height", sizeHorizontal.height * scaleRatio);
    }

    console.log("ratio=" + scaleRatio);

    var createSlotInfo = function (slot) {
        //var isVerySmall = false;
        var space = (slot.slot_width * scaleRatio) * (slot.slot_height * scaleRatio);

        if (space < 55 * 55) {
            //isVerySmall = true;
            return "<div class='info-holder' style='width: " + slot.slot_width * scaleRatio + "px; height:" + slot.slot_height * scaleRatio + "px'><div class='info info-small'><p class='slot-code-title'>" + slot.slot_code + "</p>" +
                    "</div></div>";

        } else if (space < 165 * 65) {
            return "<div class='info-holder' style='width: " + slot.slot_width * scaleRatio + "px; height:" + slot.slot_height * scaleRatio + "px'><div class='info info-medium'><p class='slot-code-title'>" + slot.slot_code + "</p>" +
                    "<p>" + slot.slot_width + "✕&#x200b;" + slot.slot_height + "px</p></div></div>";

        } else {
            return "<div class='info-holder' style='width: " + slot.slot_width * scaleRatio + "px; height:" + slot.slot_height * scaleRatio + "px'><div class='info'><p class='slot-code-title'>" + slot.slot_code + "</p>" +
                    "<p>" + slot.slot_width + "px (W) ✕ " + slot.slot_height + "px (H)</p></div></div>";
        }

    };

    var createSlot = function (i, slot, color) {
        return $("<div class='slot'/>")
                .css("background-color", "rgb(" + color.r + "," + color.g + "," + color.b + ")")
                .css("width", slot.slot_width * scaleRatio)
                .css("height", slot.slot_height * scaleRatio)
                .css("top", slot.slot_y * scaleRatio + "px")
                .css("left", slot.slot_x * scaleRatio + "px")
                .html(createSlotInfo(slot))
                .data("index", i)
                .data("slot", slot)
                .addClass("slot-code-" + slot.slot_code)//.replace(/[^A-Za-z0-9]*/g, '-'))
                .addClass("slot-index-" + i);
    }
    var buildTableSlot = function (slots) {

        var slotTable = $("#slots-table");
        slotTable.find("tbody tr").empty();

        var slotColors = generateGradient(20, 157, 202, slots.length);

        var j = 0;
        for (var i = slots.length - 1; i >= 0; i--) {
            var slot = slots[i];

            if (typeof slot.deleted !== "undefined" && slot.deleted === true) {

            } else {
                var color = slotColors[i].r + "," + slotColors[i].g + "," + slotColors[i].b;
                var domTr = $("<tr class='" + (i % 2 === 0 ? "odd" : "even") + " row-num-" + j + "'><td style='background-color:rgb(" + color + ")' class='clickable'>"
                        + slot.slot_code + "</td><td class='clickable' >"
                        + slot.slot_width + "</td><td class='clickable'>"
                        + slot.slot_height + "</td><td class='clickable'>"
                        + slot.slot_x + "</td><td class='clickable'>"
                        + slot.slot_y + "</td><td class='clickable'>"
                        + slot.slot_display_type + "</td><td>"
                        /*   + slot.ordering */
                        + ((j !== 0) ? "<span class=\"glyphicon glyphicon-circle-arrow-up go-up\"></span>" : "")
                        + ((j !== slots.length - 1) ? "<span class=\"glyphicon glyphicon-circle-arrow-down go-down\"></span>" : "")
                        + "</td></tr>");

                domTr.data("index", i);
                domTr.data("row-num", j);
                domTr.addClass("tr-index-" + i);

                slotTable.append(domTr);
            }
            j++;
        }

        /*  $.each(slots, function (i, slot) {
         
         if (typeof slot.deleted !== "undefined" && slot.deleted === true) {
         
         } else {
         var domTr = $("<tr class='" + (i % 2 === 0 ? "odd" : "even") + "'><td>"
         + slot.slot_code + "</td><td>"
         + slot.slot_width + "</td><td>"
         + slot.slot_height + "</td><td>"
         + slot.slot_x + "</td><td>"
         + slot.slot_y + "</td><td>"
         + slot.slot_display_type + "</td><td>"
         + slot.ordering + "</td></tr>");
         
         domTr.data("index", i);
         domTr.addClass("tr-index-" + i);
         
         slotTable.append(domTr);
         }
         
         
         });*/
    }

    var buildTemplate = function (allSlots) {
        domTemplate.empty();
        var slotColors = generateGradient(20, 157, 202, allSlots.length);
        $.each(allSlots, function (i, slot) {
            var color = slotColors[i];

            if (typeof slot.deleted !== "undefined" && slot.deleted === true) {

            } else {

                var domSlot = createSlot(i, slot, color);
                domTemplate.append(domSlot);
            }

        });
        buildTableSlot(allSlots);
    }

    buildTemplate(slots);

    var domDialog, domDialogCode, domDialogWidth, domDialogHeight, domDialogLeft, domDialogTop, domDialogType;
	 $( "#save-slot" ).click(function() {
	 //alert(document.getElementById("code").value);
		domDialogCode = document.getElementById("code").value; //$(domDialog).find("input[name=code]");
		domDialogWidth = document.getElementById("width").value; //$(domDialog).find("input[name=width]");
		domDialogHeight = document.getElementById("height").value;//$(domDialog).find("input[name=height]");
		domDialogLeft = document.getElementById("left").value; //$(domDialog).find("input[name=left]");
		domDialogTop = document.getElementById("top").value; //$(domDialog).find("input[name=top]");
		domDialogType = document.getElementById("type").value; //$(domDialog).find("select[name=type]");
			//document.getElementById("myForm").onsubmit = function() {myFunction()};
			 var form = document.getElementById("myForm").on("submit", function (event) {
        event.preventDefault();
        //addUser();
    });
		var form = document.myform.submit();
	});
    var applySlot = function () {
        //var domSlot;
        var index;

        index = $(domDialog).data("index");

        //var originalSlot = slots[index];
        var slot = $.extend(true, {}, $(domDialog).data("slot"));

        //console.log("editing slot : ", slot);

        slot.slot_width = parseInt(domDialogWidth.val());
        slot.slot_height = parseInt(domDialogHeight.val());
        slot.slot_x = parseInt(domDialogLeft.val());
        slot.slot_y = parseInt(domDialogTop.val());
        slot.slot_display_type = domDialogType.val();
        slot.slot_code = domDialogCode.val().replace(/\W+/g, "_");
        slot.modified = true;
        
        //if type SCROLLTEXT then only one can exist
        if(slot.slot_display_type === "SCROLLTEXT"){
            var exist = false;
            $.each(slots, function(i, slot){
                if(typeof slot.deleted !== 'undefined' && slot.deleted === true){
                }
                else if (slot.slot_display_type === "SCROLLTEXT"){
                    exist = true;
                }
            });
            if(exist === true){
                alert("Only one slot can be SCROLLTEXT");
                return;
            }
        }

        // shold be no same slot code
        var sameSlotCode = $(".slot-code-" + slot.slot_code);
        var currentCount = sameSlotCode.length;
        if (index !== null) {
            $.each(sameSlotCode, function (i, domSameSlot) {
                if ($(domSameSlot).is(".slot-index-" + index)) {
                    currentCount--;
                }
            });
        }

        if (currentCount > 0) {
            // error
            alert("Slot code %s has been used already. Please choose another slot code.".replace("%s", slot.slot_code));
            
            return;
        } else {


            $.ajax({
                type: "POST",
                url: "page_template_validate_slot.php",
                data: slot,
                success: function (data) {

                    if (parseInt(data.status) === 1) {
                        isSaveRequired = true;

                        if (index === null) {
                            // this is new slot, 
                            index = slots.length;
                            slots[index] = slot;
                            slot.slot_code = domDialogCode.val().replace(/\W+/g, "_");

                        } else {
                            // this is editing
                            // console.log("getting dom slot from " + index);
                            slots[index] = slot;
                        }
                        buildTemplate(slots);
                        domDialog.dialog("close");

                    } else {
                        alert(data.message);
                    }
                },
                error: function () {
                    alert("Unable to validate slot");
                },
                dataType: "json"
            });
        }
    };

    var deleteSlot = function () {
        var domSlot;
        var index;

        //var originalSlot = slots[index];
        var slot = $(domDialog).data("slot");

        index = $(domDialog).data("index");
        domSlot = $(domTemplate).find(".slot-index-" + index);

        slot.modified = true;
        slot.deleted = true;

        slots[index] = slot;

        buildTemplate(slots);
        domDialog.dialog("close");
        isSaveRequired = true;

    }

   /* domDialog = $("#dialog-form").dialog({
        autoOpen: false,
        height: 400,
        width: 500,
        modal: true,
        open: function (event, ui) {

        },
        buttons: {
            "OK": applySlot,
            "Delete": deleteSlot,
            "Cancel": function () {
                domDialog.dialog("close");
            }


        },
        close: function () {
            //form[ 0 ].reset();
            //allFields.removeClass( "ui-state-error" );
        }
    });

    domDialogCode = $(domDialog).find("input[name=code]");
    domDialogWidth = $(domDialog).find("input[name=width]");
    domDialogHeight = $(domDialog).find("input[name=height]");
    domDialogLeft = $(domDialog).find("input[name=left]");
    domDialogTop = $(domDialog).find("input[name=top]");
    domDialogType = $(domDialog).find("select[name=type]");

    var form = domDialog.find("form").on("submit", function (event) {
        event.preventDefault();
        //addUser();
    });*/

    var onClickSlotBox = function (domSlot) {
        var index = $(domSlot).data("index");
        var slot = slots[index];
		//console.log(slot);
		domDialogCode = $("#code"); 
		domDialogWidth = $("#width");
		domDialogHeight = $("#height");
		domDialogLeft = $("#left");
		domDialogTop = $("#top");
		domDialogType = $("#type");
        //var slot = $(this).data("slot");

        domDialogCode.val(slot.slot_code);
        domDialogWidth.val(slot.slot_width);
        domDialogHeight.val(slot.slot_height);
        domDialogLeft.val(slot.slot_x);
        domDialogTop.val(slot.slot_y);
        domDialogType.val(slot.slot_display_type);
		$("#dialog-form").attr('action', 'update');
		$("#modal-content").modal();
		
        //domDialog.data("index", index);
        //domDialog.data("slot", slot);
        //domDialog.data("action", "update");
        //domDialog.attr("title", "Xyz");
        //domDialog.dialog("open");

        // domDialog.find(".ui-dialog-title").text( "Xyz");
    }

    domTemplate.on("click", ".slot", function () {
        onClickSlotBox(this);
    });

    domTemplate.on("mouseover", ".slot", function () {
        $("#slots-table").find(".tr-index-" + $(this).data("index") + " td").addClass("highlighted")

    });
    domTemplate.on("mouseout", ".slot", function () {
        $("#slots-table").find(".tr-index-" + $(this).data("index") + " td").removeClass("highlighted")
    });

    var onClickAddSlot = function () {
        var slot = {
            slot_id: null,
            slot_code: "",
            orientation: template.orientation,
            slot_height: "",
            slot_width: "",
            slot_x: "",
            slot_y: "",
            group: null,
            page_template_id: template.page_template_id,
            multiple_status: "1",
            slot_display_type: "",
            ordering: null,
            remark: null,
            user_created: null,
            date_created: null,
            user_modified: null,
            date_modified: null};


        // get the last data in the slots
        if (slots.length > 0) {
            var lastSlot = slots[slots.length - 1];
            slot.ordering = parseInt(lastSlot.ordering) + 1;
        } else {
            slot.ordering = 1;
        }
        domDialogCode.val(slot.slot_code);
        domDialogWidth.val(slot.slot_width);
        domDialogHeight.val(slot.slot_height);
        domDialogLeft.val(slot.slot_x);
        domDialogTop.val(slot.slot_y);

        domDialog.data("slot", slot);
        domDialog.data("action", "add");

        domDialog.data("slot", slot);
        domDialog.data("index", null);
        domDialog.dialog("open");
    };

    $("#add-slot").click(onClickAddSlot);

    var onClickSaveTemplate = function () {
        isSaveRequired = false;
        var domSaveTemplateForm = $("#template-data-form");
        var updatedSlots = [];
        var j = 0;

        // collect the edited slot, but not new and deleted
        $.each(slots, function (i, slot) {
            if (typeof slot.modified !== 'undefined' && slot.modified === true) {

                if (typeof slot.deleted !== 'undefined' && slot.deleted === true) {
                    if (slot.slot_id === null) {
                        return;
                    }
                }
                updatedSlots[j] = slot;
                j++;
            }
        });

        domSaveTemplateForm.find("input[name=slots]").val(JSON.stringify(updatedSlots));
        domSaveTemplateForm.submit();
    };

    $("#save-template").click(onClickSaveTemplate);

    $(window).bind('beforeunload', function () {

        if (isSaveRequired === true) {
            return "You have not save the template yet!";
        }
    });

    // highlight slot when hover table row
    $("#slots-table").on("mouseover", "tbody td", function () {
        var index = $(this).parent().data("index");
        var domSlot = $(".slot-index-" + index);
        domSlot.addClass("highlighted");
    });
    // unhighlight slot when hover out table row
    $("#slots-table").on("mouseout", "tbody td", function () {
        var index = $(this).parent().data("index");
        var domSlot = $(".slot-index-" + index);
        domSlot.removeClass("highlighted");
    });

    // make table row is clickable
    $("#slots-table").on("click", ".clickable", function () {
        var index = $(this).parent().data("index");
        var domSlot = $(".slot-index-" + index);
        domSlot.click();
    })

    $("#slots-table").on("click", ".go-up", function () {
        var index = $(this).parent().parent().data("index");
        //var row = $(this).parent().parent().data("row-num");
        //console.log("up at index " + index + " row=" + row);

        // hold the value
        var slot = slots[index];

        // step one remove the current value
        slots.splice(index, 1);

        slots.splice(index + 1, 0, slot);

        var j = 0;
        $.each(slots, function (i, slot) {
            if (typeof slot.deleted !== "undefined" && slot.deleted === true) {

            } else {
                if (slot.ordering !== j) {
                    slot.ordering = j;
                    slot.modified = true;
                }
                j++;
            }
        });

        buildTemplate(slots);

        this.scrollIntoView();

    });

    $("#slots-table").on("click", ".go-down", function () {
        var index = $(this).parent().parent().data("index");

        //var row = $(this).parent().parent().data("row-num");
        //console.log("down at index " + index + " row=" + row);

        // hold the value
        var slot = slots[index];

        // step one remove the current value
        slots.splice(index, 1);

        slots.splice(index - 1, 0, slot);

        var j = 0;

        $.each(slots, function (i, slot) {

            if (typeof slot.deleted !== "undefined" && slot.deleted === true) {

            } else {

                if (slot.ordering !== j) {
                    slot.ordering = j;
                    slot.modified = true;
                }
                j++;
            }
        });

        buildTemplate(slots);

        this.scrollIntoView();

    });
   
    $("#cancel-edit").click(function(){
       window.location = "page_template.php#row-" 
    });

    //});
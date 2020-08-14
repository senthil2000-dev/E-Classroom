$(document).ready(function() {
    $(".navShowHide").on("click",function(){
        var main=$("#mainSectionContainer");
        var nav=$("#sideBarContainer");
        if(main.hasClass("leftPadding")){
            nav.hide();
        }
        else{
            nav.show();
        }

        main.toggleClass("leftPadding");

    });

    $("#mainContentContainer").on("click", function() {
        var main=$("#mainSectionContainer");
        var nav=$("#sideBarContainer");
        if(main.hasClass("leftPadding")){
            nav.hide();
            main.toggleClass("leftPadding");
        }
        
    });

});




function notSignedIn() {
    alert("You must be signed in to perform this action");
}

function notSignedIn2(button) {
    if (confirm("You must be signed in to view your profile, click OK to continue to SignIn page and CANCEL if you wish to continue watching")) {
        $(button).attr("href", "signIn.php");
    } 
}

$(document).ready(function() {
    $("#file-upload").on("submit", function (event) {
        event.preventDefault();
        const formData = new FormData($("#file-upload")[0]);
        // or like this
        // const myForm = document.getElementById("subscribeForm");
        // const formData = new FormData(myForm);
        $.ajax({
            url: 'ajax/updateProfilePic.php',
            method: 'POST',
            data: formData,
            contentType:false,
            cache:false,
            processData:false,
            success: function(response) {
                $(".profileImage").attr("src", response);
                $(".profilePicture").attr("src", response);
            }
        })
    });
    
});

function submitForm() {
    $("#press").click();
}

function callf(url) {
    if(url!="")
    window.location=url;
}

function submitForm2() {
    document.getElementById('submitForm').submit();
}

function status() {
    if(document.getElementById("checking").checked) {
        $word="pause";
    }
    else {
        $word ="resume";
    }
       if (confirm("Do you want to "+$word+ " your watch history")) {
        $.post("ajax/status.php")
        .done(function(){
            console.log("done");
        });
    }   
}

function status2() {
    if(document.getElementById("checking").checked) {
        $word="pause";
    }
    else {
        $word ="resume";
    }
       if (confirm("Do you want to "+$word+ " your search history")) {
        $.post("ajax/status2.php")
        .done(function(){
            console.log("done");
        });
    }   
}

function addTo(button, id) {
            var l=$("#added .videoSegment").length;
            var operation=$(button).find(".thumbnail").hasClass("selected")?0:1;
            var i=operation?1:-1;
            console.log(l+i);
            if((l+i)>0) {
               $("#playlistName").removeAttr("hidden");
            }
            else {
                $("#playlistName").attr("hidden", true);
            }
            
            $.post("playlist.php", {id:id, operation:operation})
            .done(function(html){
                if(operation==1) {
                    $("#added").append(html);
                }
                else if(operation==0) {
                    $("#"+id).remove();
                }
                $(button).find(".thumbnail").toggleClass("selected"); 
            });
        }

$(document).ready(function(){
    $('.deleteSearch').click(function(event){
        event.preventDefault();
        myFunction(event);
        });
    });

function myFunction(event) {
    $.post("ajax/delete.php", {id: event.target.name})
            .done(function(n){
                console.log(n);
                $(event.target).closest(".historyItem").remove();
                if(n==1) {
                    $(".searchFound").text(n+" search found");
                }
                else if(n==0) {
                    $(".searchFound").text("Your search history is empty");
                }
                else {
                    $(".searchFound").text(n+" searches found");
                }
                            });
                }

function deleteAll() {
    $.post("ajax/deleteAll.php")
            .done(function(){
                $(".searchFound").text("Your search history is empty");
                $(".historyItem").remove();
            });
}

function deleteAll2() {
    $.post("ajax/deleteAll2.php")
            .done(function(){
                $(".left").text("Your watch history is empty");
                $(".videoMatrix a").remove();
            });
}

function hover(element) {
    element.setAttribute('src', 'assets/images/icons/52-512.webp');
  }
  
  function unhover(element) {
    element.setAttribute('src', 'assets/images/icons/deletefull.png');
  }

  $(document).ready(function(){
  $('.dropbtn').click(function(event){
    event.preventDefault();
    showDropdown(event);
    });
  });

  $(document).ready(function(){
    $('.removing').click(function(event){
      event.preventDefault();
      removeItem(event);
      });
    });

function showDropdown(event) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show')) {
            if(event.target.nextSibling.nextSibling!=openDropdown)
                openDropdown.classList.remove('show');
        }
    }
    console.log(event.target.classList[4]);
    document.getElementById(event.target.classList[4]).classList.toggle("show");
}

window.onclick = function(event) {
    console.log(event.target);
    if (event.target == document.getElementById("myModal")) {
        document.getElementById("myModal").style.display = "none";
      }
    var dropdowns = document.getElementsByClassName("dropdown-content");
    if (!event.target.matches('.dropbtn')) {
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

function removeItem(event) {
    $.post("ajax/deleteWatch.php", {id: event.target.classList[1]})
            .done(function(n){
                console.log(n);
                if(n==0) {
                    console.log("1");
                    $(".left").text("Your watch history is empty");
                }
                $(event.target).closest(".flexing").remove();
            });
}

$(document).ready(function(){
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("btn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close1")[0];
console.log(btn);
// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

});
function startPopulate() {
    /* user = getCookie("uer");
    if (user == "") {
        alert("Please Log in first.");
        location.replace("http://172.18.76.150/darwin/BidSource/beta3/dashboard/login.html");
    } // if not logged in then go back */

    // this will populate the site with any new jobs 
    version = 'directory'
    inputText = 'nothing'
    total = "version=".concat(version, "& param=", inputText); // This will hold the data to send
    makeCalls(total); // makes a sql call and gets the data returned
}

async function makeCalls(total) {
    //console.log(total);
    let responsive = await fetch("processing.php", {
        method: 'post',
        headers: {
            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        body: total
    });
    let data = await responsive.json();
    //console.log(responsive.ok);
    if (responsive.ok == false || responsive.status !== 200) {
        alert("Request Failed ");
        console.log('Network response was not ok.');
        //makeCalls(total);
        return;
    }
    if (data.Done == "yes") {
        document.getElementById('rows').innerHTML = data.Data;
    } else {
        document.getElementById("rows").innerHTML = "Could not reach the server to retrieve data. " + total;
        console.log("Failed: " + total);
        makeCalls(total); // this shouldnt get reached if the response came in, but just incase the message came in but the response wasnt the correct json format
    }
    return ("Yes");
}

function openSite(websiteID) {
    userID = getCookie("unu");
    logIt("User " + userID + " Opened Directory# " + websiteID, 0, 0, userID, "open directory");
    sessionStorage.setItem('websiteID', websiteID);
    location.replace('work.html');
}


function logIt(workString, elapsed, pending, userid, type) {
    command = 'INSERT INTO `BidSource`.`worklog`(`workstring`,`elapsedtime`,`pendingid`,`userid`,`type`,`date`) VALUES("' + workString + '",' + elapsed.toString() + ',' + pending.toString() + ',' + userid.toString() + ',"' + type + '", now());';
    total = "version=".concat("sql", "& data=", command); // This will hold the data to send
    makeSQL(total); // this just sends the sql command and doesnt wait for anything
    console.log("Updated Log.");
}


async function makeSQL(total) {
    let responsive = await fetch("processing.php", {
        method: 'post',
        headers: {
            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        body: total
    });
    let data = await responsive;
    if (responsive.ok == false || responsive.status !== 200) {
        alert("Request Failed ");
        console.log('Network response was not ok.');
        return;
    }

    return ("Yes");
}


function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}
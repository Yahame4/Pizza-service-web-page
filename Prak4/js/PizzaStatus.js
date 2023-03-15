function process(pizzalist) {
    "use strict";
    const customer = document.getElementById('customerDiv');

    if (pizzalist == "Error: orderid for session missing.") {
      customer.removeChild(customer.firstChild);
      let para = document.createElement('p');
      para.innerText = "Es liegt keine Bestellung von Ihnen vor!";
      document.getElementById('customerDiv').appendChild(para);
      return; 
    }

    if (pizzalist == "Es liegt keine Bestellung von dir vor!"){
      customer.removeChild(customer.firstChild);
      let para = document.createElement('p');
      para.innerText = "Es liegt keine Bestellung von Ihnen vor!";
      document.getElementById('customerDiv').appendChild(para);
      return;
    }
    
    pizzalist = JSON.parse(pizzalist);  
 
    while (customer.firstChild) {
      customer.removeChild(customer.firstChild);
    }

    pizzalist.forEach((pizza) => {
      let para = document.createElement('p');
      para.innerText = pizza.name + ": " + processStatus(pizza.status);
      customer.appendChild(para);
    });
  };

  function processStatus(status) {
    if(status == 0) return "bestellt";
    if(status == 1) return "im Ofen";
    if(status == 2) return "fertig";
    if(status == 3) return "unterwegs";
    if(status == 4) return "geliefert";
    return "default";
  }
  
  var request = new XMLHttpRequest(); 
  
  function requestData() { 
    request.open("GET", "KundenStatus.php"); 
    request.onreadystatechange = processData; 
    request.send(null); 
  }
  
  function processData() {
    if(request.readyState == 4) { 
       if (request.status == 200) {   
         if(request.responseText != null) 
           process(request.responseText);
         else console.error ("Dokument ist leer");        
       } 
       else console.error ("Uebertragung fehlgeschlagen");
    } else ;        
  }
  
  setInterval(requestData, 2000);
  
"use strict";

class Cart {
  constructor() {
    this.cart = []; 
  }

  cartLength() {
    return this.cart.length;
  }

  totalPrice() {
    "use strict";
    let price = 0;
    this.cart.forEach((value) => {
      price += value[2];
    });
    let para = document.getElementById("currentPrice");
    para.innerText = "Gesamtpreis der Bestellung: " + price.toFixed(2) + " €";
  }

  addPizza(pizzaId, pizzaName, pizzaPrice) {
    "use strict";
    this.cart.push([pizzaId, pizzaName, pizzaPrice]);
    let select = document.getElementById("pizza");
    let option = document.createElement("option");
    option.appendChild(document.createTextNode(pizzaName));
    option.value = pizzaId;
    select.appendChild(option);
    console.log(this.cart);

    this.totalPrice();
    buttonEnableDisable();
  }

  deleteAll() {
    "use strict";
    this.cart = [];
    this.price = 0;
    let select = document.getElementById("pizza");
    while(select.length != 0) {
      select.remove(select.length-1);
    }
    this.totalPrice();
    buttonEnableDisable();
  }
  
  deleteSelected() {
    "use strict";
    let select = document.getElementById("pizza");
    let i = 0;
    while(i < select.length) {
      if (!select[i].selected) {
        i++;
        continue;
      }
      select.remove(i);
      this.cart.splice(i, 1);
    }
    this.totalPrice();
  }
}

let shoppingcart = new Cart();

function orderForm() {
  "use strict";
  let textField = document.getElementById("address").value;
  if (!textField) {return false};
  if (!shoppingcart.cartLength()){
      return false; 
  }
  return true;
}
 
function buttonEnableDisable() {
  "use strict";
  let submitButton = document.getElementById("submit");
  if (orderForm()) {
      submitButton.disabled = false;
    }
  else submitButton.disabled = true;
}

function onSubmit() {
  "use strict";
  let select = document.getElementById("pizza");
  for (let i = 0; i < select.length; i++) {
    select[i].selected = true;
  }
}

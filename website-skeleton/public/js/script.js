window.onload = () => {
    let links = document.querySelectorAll("[data-delete]")
    
    for(link of links){
        // On écoute le clic
        link.addEventListener("click", function(e){
            e.preventDefault()

            if(confirm("Voulez-vous supprimer ce média ?")){
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    // On récupère la réponse en JSON
                    response => response.json()
                ).then(data => {
                    if(data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
    
}



const newItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
  
    const item = document.createElement("div");
    item.classList.add("col-4");
    item.innerHTML = collectionHolder
      .dataset
      .prototype
      .replace(
        /__name__/g,
        collectionHolder.dataset.index
      );
  
    item.querySelector(".btn-remove").addEventListener("click", () => item.remove());
  
    collectionHolder.appendChild(item);
  
    collectionHolder.dataset.index++;
  };
  
  document
    .querySelectorAll('.btn-remove')
    .forEach(btn => btn.addEventListener("click", (e) => e.currentTarget.closest(".col-4").remove()));
  
  document
    .querySelectorAll('.btn-new')
    .forEach(btn => btn.addEventListener("click", newItem));

    
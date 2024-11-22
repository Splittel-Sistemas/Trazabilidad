function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
}


function allowDrop(event) {
    event.preventDefault();
}


function drop(event) {
    event.preventDefault();
    var data = event.dataTransfer.getData("text");
    var draggedRow = document.getElementById(data);

    
    var newRow = document.createElement('tr');
    newRow.id = draggedRow.id; 
    newRow.innerHTML = draggedRow.innerHTML + `<td><button class="btn btn-warning" onclick="regresarRow('${draggedRow.id}')">Regresar</button></td>`;
    
   
    document.getElementById('table-2-content').appendChild(newRow);

    
    draggedRow.style.display = "none";
}


function regresarRow(rowId) {
    
    var table2Row = document.querySelector(`#table-destination #${rowId}`);
    if (!table2Row) return; 
    
    var table1 = document.getElementById('table-source').getElementsByTagName('tbody')[0];

   
    var table1Row = document.getElementById(rowId);
    if (table1Row) {
       
        table1Row.style.display = '';
    } else {
        
        var newRow = document.createElement('tr');
        newRow.id = rowId; 
        newRow.innerHTML = table2Row.innerHTML.replace(/<td>.*?Regresar.*?<\/td>/, ''); 
        newRow.setAttribute('draggable', 'true'); 
        newRow.setAttribute('ondragstart', 'drag(event)'); 
        table1.appendChild(newRow);
    }

    
    table2Row.remove();
}

document.getElementById('dropzone').addEventListener('dragover', allowDrop);
document.getElementById('dropzone').addEventListener('drop', drop);
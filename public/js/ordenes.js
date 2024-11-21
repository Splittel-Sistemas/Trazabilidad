

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
        newRow.innerHTML = draggedRow.innerHTML + `<td><button class="btn btn-warning" onclick="regresarRow('${data}')">Regresar</button></td>`;
        
        
        document.getElementById('table-2-content').appendChild(newRow);

        
        draggedRow.style.display = "none";
    }

  
    function regresarRow(rowId) {
        var row = document.getElementById(rowId);
        var table1 = document.getElementById('table-source').getElementsByTagName('tbody')[0];

        
        var newRow = document.createElement('tr');
        newRow.id = rowId;
        newRow.innerHTML = row.innerHTML.replace('<td><button class="btn btn-warning" onclick="regresarRow(\'' + rowId + '\')">Regresar</button></td>', '');

        
        table1.appendChild(newRow);

        
        row.remove();
    }

    
    document.getElementById('dropzone').addEventListener('dragover', allowDrop);
    document.getElementById('dropzone').addEventListener('drop', drop);

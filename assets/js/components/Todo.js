import React, { Component } from "react";
import $ from 'jquery';

class Todo extends Component {
    constructor() {
        super();
        this.userInput = "";
        this.todoList = [];
    }

    render() {
        return (
            <div>
                <h1 align="center"> Todo List</h1 >
                <input
                    value={this.userInput}
                    type="text"
                    placeholder="Entrer un truc (taille max 255 caractères)"
                    onChange={this.inputChange.bind(this)}
                    className="form-control mb-2"
                />
                <button
                    onClick={this.addTodo.bind(this)}
                    className="btn btn-success"
                >Ajouter
                </button>
                <div>
                    {this.renderTodos()}
                </div>
            </div>
        );
    };

    getToDoList() {
        try {
            var dataServer = $.ajax({
                type: 'POST',
                dataType: 'json',
                async: false,
                url: 'http://localhost:8000/GetTodo',
                success: function (data) {
                    return data;
                }
            }).responseJSON;

            // Reset array
            this.todoList = [];
            for (let index = 0; index < dataServer["id"].length; index++)
                this.todoList.push({ id: dataServer["id"][index], memo: dataServer["memo"][index], dateAjout: dataServer["dateAjout"][index], priority: dataServer["priority"][index] });

            this.setState({}); // Pour update les variables dans le contenu HTML de Render()
        } catch { }
    }

    addTodo() {
        if (this.userInput !== "" && this.userInput.length < 256) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'http://localhost:8000/InsertTodo',        // Script Cible
                data: "data=" + JSON.stringify(this.userInput),
                success: function () { }
            });
            this.userInput = "";
            this.getToDoList();
        } else if (this.userInput.length > 255) alert("Chaîne de caractère max : 255. \nVotre message fait : " + this.userInput.length + " caractères.\nVeuillez supprimer des caractères.");
    }

    inputChange(event) {
        this.userInput = event.target.value;
        this.setState({}); // Pour update les variables dans le contenu HTML de Render()
    }

    renderTodos() {
        return this.todoList.map((todo) => {
            return (
                <ul className="list-group">
                    <li className="list-group-item">
                        <div>
                            <div style={{ display: 'inline-block', width: "90%" }}>
                                <div style={{ color: "blue" }}>Ajouté le {todo.dateAjout}</div>
                                <div>{todo.memo}</div>
                            </div>
                            <div style={{ display: 'inline-block', float: "right" }}>
                                <button style={{ margin: "0vh 0vh 1vh 2vh" }} id={todo.id} onClick={this.deleteTodo.bind(this)} className="btn btn-danger">X</button>
                                <select className="form-select form-select-lg mb-3" name={todo.id} id={todo.priority} onChange={this.selectPriorityChange}>
                                    {this.boxPriority(todo.priority)}
                                </select>
                            </div>
                        </div>
                    </li>
                </ul>
            )
        })
    }

    boxPriority(number) {
        return this.todoList.map((todo) => {
            if (number == todo.priority) {
                return (
                    <option value={todo.priority} selected>{todo.priority}</option>
                )
            }
            else return (
                <option value={todo.priority}>{todo.priority}</option>
            )
        })
    }

    selectPriorityChange = (event) => {
        const dataToServer = {
            id: event.target.name,
            priorityActual: event.target.id,
            newPriority: event.target.value
        }
        event.target.value = event.target.id;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            async: false,
            data: "data=" + JSON.stringify(dataToServer),
            url: 'http://localhost:8000/ChangePriorityTodo',
            success: function (data) { }
        });
        this.getToDoList();
    }

    deleteTodo(event) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            async: false,
            data: "data=" + JSON.stringify(event.target.id),
            url: 'http://localhost:8000/DeleteTodo',
            success: function () { }
        })
        this.getToDoList();
    }

    componentDidMount() {
        this.getToDoList();
    }

}

export default Todo;

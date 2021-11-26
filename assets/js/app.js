import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router } from 'react-router-dom';
import Todo from './components/Todo';

ReactDOM.render(<Router><Todo /></Router>, document.getElementById('root'));

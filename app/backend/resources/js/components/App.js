import React from 'react';
import ReactDOM from 'react-dom';
import {createStore} from 'redux';
import {Provider} from 'react-redux';
import {BookReducer} from "../reducers/BookReducer.js";
import BookList from "./BookList.js";
import BookEdit from "./BookEdit.js";
import BookCreate from "./BookCreate.js";
import {BrowserRouter as Router, Switch, Route, Link} from 'react-router-dom';
import {persistStore, persistReducer} from 'redux-persist'
import storage from 'redux-persist/lib/storage'
import {PersistGate} from 'redux-persist/integration/react'
import ErrorBoundary from "./ErrorBoundary";
import '../../css/main.scss';

const persistConfig = {
    key: 'backend',
    storage,
};

const persistedReducer = persistReducer(persistConfig, BookReducer);
const store = createStore(persistedReducer);
const persistor = persistStore(store);

if (document.getElementById('book-container')) {
    ReactDOM.render(
        <ErrorBoundary>
            <Router>
                <Provider store={store}>
                    <PersistGate loading={<div>Loading ...</div>} persistor={persistor}>
                        <Switch>
                            <Route exact path="/">
                                <BookList/>
                            </Route>
                            <Route path="/book/edit/:id">
                                <BookEdit/>
                            </Route>
                            <Route path="/book/create">
                                <BookCreate/>
                            </Route>
                        </Switch>
                    </PersistGate>
                </Provider>
            </Router>
        </ErrorBoundary>, document.getElementById('book-container')
    );
}



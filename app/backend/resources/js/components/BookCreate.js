import React, {Component} from 'react';
import {connect} from "react-redux";
import {
    bookCreateRequestFinished,
    bookCreateRequestFailed,
    bookCreateRequestErrors,
    sendingCreateBookRequest,
    bookCreateReset
} from '../actions/BookActions';

import {withRouter} from 'react-router-dom';

class BookCreate extends Component {
    constructor(props) {
        super(props);
        this.submit = this.handleSubmit.bind(this);
    }

    handleSubmit(e) {
        e.preventDefault();
        this.props.dispatch(sendingCreateBookRequest());
        const data = new FormData(e.target);
        this.sendRequest(data);
    }

    componentDidMount() {
        this.props.dispatch(bookCreateReset());
    }

    sendRequest(data) {
        fetch(`${address}/home/create`, {
            method: 'POST',
            headers: {
                'X-REQUESTED-WITH': 'XMLHttpRequest'
            },
            body: data,
        })
            .then(response => response.json())
            .then(
                response => {
                    if (response.errors) {
                        this.props.dispatch(bookCreateRequestErrors(response.errors));
                    } else if (response.success) {
                        this.props.dispatch(bookCreateRequestFinished(response.books));
                        this.props.history.goBack();
                    }
                }
            )
            .catch(() => this.props.dispatch(bookCreateRequestFailed()));
    }

    render() {
        let content = null;
        if (this.props.sending || this.props.sendingFetch) {
            content = <div>Loading ...</div>;
        } else if (this.props.failed || this.props.failedFetch) {
            content = <div>Error occurred</div>;
        } else {
            content = (
                <form className="edit-form" onSubmit={this.submit}>
                    <label>Name:</label>
                    <input type="text" name="name"/>
                    <div
                        className="validation-error">{this.props.errors && this.props.errors.name ? this.props.errors.name : ''}</div>
                    <label>Price:</label>
                    <input type="number" step=".01" name="price"/>
                    <div
                        className="validation-error">{this.props.errors && this.props.errors.price ? this.props.errors.price : ''}</div>
                    <label>Description:</label>
                    <textarea name="short_description"/>
                    <div
                        className="validation-error">{this.props.errors && this.props.errors.short_description ? this.props.errors.short_description : ''}</div>
                    <label>Authors:</label>
                    {
                        this.props.authors.map(author =>
                            <div key={"author" + author.id} className="checkbox-container">
                                <input className="checkbox-input" type="checkbox" id={"authors" + author.id}
                                       name="authors[]" value={author.id}/>
                                <label className="checkbox-label" htmlFor={author.id}>{author.name}</label>
                            </div>
                        )
                    }
                    <div
                        className="validation-error">{this.props.errors && this.props.errors.authors ? this.props.errors.authors : ''}</div>
                    <label>Genres:</label>
                    {
                        this.props.genres.map(genre =>
                            <div key={"genre" + genre.id} className="checkbox-container">
                                <input className="checkbox-input" type="checkbox" key={"genres" + genre.id}
                                       id={"genre" + genre.id} name="genres[]" value={genre.id}/>
                                <label className="checkbox-label" htmlFor={genre.id}>{genre.name}</label>
                            </div>
                        )
                    }
                    <div
                        className="validation-error">{this.props.errors && this.props.errors.genres ? this.props.errors.genres : ''}</div>

                    <input type="submit" value="Send"/>
                </form>
            );
        }
        return content;

    }

}

const mapStateToProps = (state) => {
    return {
        sending: state.sendingCreateBookRequest,
        failed: state.requestCreateFailed,
        errors: state.requestCreateErrors,
        sendingFetch: state.sendingCreateFetchBookRequest,
        failedFetch: state.requestCreateFetchFailed,
        authors: state.authors,
        genres: state.genres
    };
};

export default withRouter(connect(mapStateToProps)(BookCreate));
import React, {Component} from 'react';
import {connect} from "react-redux";

import {
    sendingEditBookRequest,
    bookEditRequestFinished,
    bookEditRequestFailed,
    bookEditRequestErrors,
    bookEditReset,
} from '../actions/BookActions';

import {withRouter} from 'react-router-dom';

class BookEdit extends Component {
    constructor(props) {
        super(props);
        this.submit = this.handleSubmit.bind(this);
    }

    handleSubmit(e) {
        e.preventDefault();
        //this.props.dispatch(sendingEditBookRequest());
        const data = new FormData(e.target);
        this.sendRequest(data);
    }

    countDecimals(value) {
        if ((value % 1) !== 0)
            return value.toString().split(".")[1].length;
        return 0;
    }

    checkDecimal(e) {
        let el = e.target.value;
        let float = parseFloat(el);
        let countDecimals = this.countDecimals(float);
        if (countDecimals > 2) {
            e.target.value = float.toFixed(2);
        }
    }

    componentDidMount() {
        this.props.dispatch(bookEditReset());
    }

    sendRequest(data) {
        fetch(`${address}/home/edit/${this.props.book.bookInfo.id}`, {
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
                        this.props.dispatch(bookEditRequestErrors(response.errors, response.failedModel));
                    } else if (response.success) {
                        this.props.dispatch(bookEditRequestFinished(response.books));
                        this.props.history.goBack();
                    }
                }
            )
            .catch(() => this.props.dispatch(bookEditRequestFailed()));
    }

    render() {
        let content = '';
        if (this.props.sending || this.props.sendingFetch) {
            content = <div>Loading ...</div>;
        } else if (this.props.failed || this.props.failedFetch) {
            content = <div>Error occurred</div>;
        } else {
            let failedModelAuthors = (this.props.failedModel && this.props.failedModel.authors) ? this.props.failedModel.authors.map(Number) : [];
            let failedModelGenres = (this.props.failedModel && this.props.failedModel.genres) ? this.props.failedModel.genres.map(Number) : [];
            content = (
                <form className="edit-form" onSubmit={this.submit}>
                    <label>Name:</label>
                    <input defaultValue={
                        this.props.failedModel ? this.props.failedModel.fields.name :this.props.book.bookInfo.name
                    } type="text" name="name"/>
                    <div className="validation-error">{this.props.errors && this.props.errors.name ? this.props.errors.name : ''}</div>

                    <label>Price:</label>
                    <input defaultValue={
                        this.props.failedModel ? this.props.failedModel.fields.price :this.props.book.bookInfo.price
                    } type="number" step=".01" name="price"  onChange={(event) => this.checkDecimal(event.nativeEvent)}/>
                    <div className="validation-error">{this.props.errors && this.props.errors.price ? this.props.errors.price : ''}</div>

                    <label>Description:</label>
                    <textarea defaultValue={
                        this.props.failedModel ? this.props.failedModel.fields.short_description : this.props.book.bookInfo.short_description
                    } name="short_description"/>
                    <div className="validation-error">{this.props.errors && this.props.errors.short_description ? this.props.errors.short_description : ''}</div>

                    <label>Authors:</label>
                    {
                        this.props.authors.map(author =>
                            <div key={"author" + author.id} className="checkbox-container">
                                <input defaultChecked={
                                            (this.props.failedModel ) ? (failedModelAuthors ? failedModelAuthors.includes(author.id) : false)
                                            : this.props.book.authors.includes(author.id)
                                        }
                                        className="checkbox-input"
                                        type="checkbox" id={"author" + author.id} name="authors[]" value={author.id}/>
                                <label className="checkbox-label" htmlFor={author.id}>{author.name}</label>
                            </div>
                        )
                    }
                    <div className="validation-error">{this.props.errors && this.props.errors.authors ? this.props.errors.authors : ''}</div>

                    <label>Genres:</label>
                    {
                        this.props.genres.map(genre =>
                            <div key={"genre" + genre.id} className="checkbox-container">
                                <input defaultChecked={
                                            (this.props.failedModel ) ? (failedModelGenres ? failedModelGenres.includes(genre.id) : false)
                                            : this.props.book.genres.includes(genre.id)
                                        }
                                       className="checkbox-input"
                                       type="checkbox" key={"genre" + genre.id} id={"genre" + genre.id} name="genres[]"
                                       value={genre.id}/>
                                <label className="checkbox-label" htmlFor={genre.id}>{genre.name}</label>
                            </div>
                        )
                    }
                    <div className="validation-error">{this.props.errors && this.props.errors.genres ? this.props.errors.genres : ''}</div>

                    <input type="submit" value="Send"/>
                </form>
            );
        }
        return content;

    }

}

const mapStateToProps = (state) => {
    return {
        sending: state.sendingEditBookRequest,
        failed: state.requestEditFailed,
        errors: state.requestEditErrors,
        sendingFetch: state.sendingEditFetchBookRequest,
        failedFetch: state.requestEditFetchFailed,
        book: state.editBook,
        authors: state.authors,
        genres: state.genres,
        failedModel: state.requestEditFailedModel
    };
};

export default withRouter(connect(mapStateToProps)(BookEdit));
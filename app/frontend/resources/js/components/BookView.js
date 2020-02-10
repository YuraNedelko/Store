import React, {Component} from 'react';
import {connect} from 'react-redux';

import {
    resetViewFormVisibility,
    showViewForm,
} from "../actions/BookActions";
import BookOrderForm from "./BookOrderForm";


class BookView extends Component {
    constructor(props) {
        super(props);
        this.show = this.showForm.bind(this);
    }

    componentDidMount() {
        this.props.dispatch(resetViewFormVisibility());
    }

    showForm(){
        this.props.dispatch(showViewForm());
    }

    render() {
        let content = null;
        let requestState = null;

        if (this.props.formRequestSuccess) {
            requestState = (
                <div className="alert alert-success" role="alert">
                    Request was successfully sent!
                </div>
            );
        }
        if (this.props.sendingFetch || this.props.sendingFormRequest) {
            content = <div>Loading ...</div>;
        } else if (this.props.failedFetch) {
            content = <div>Error occurred</div>;
        } else {
            if(!this.props.showForm){
                content = (
                    <div>
                        {requestState}
                        <div className="book-view-container">
                            <label>Name:</label>
                            <div> {this.props.book.bookInfo.name} </div>

                            <label>Price:</label>
                            <div> {this.props.book.bookInfo.price} $ </div>

                            <label>Description:</label>
                            <textarea readOnly={true} value={this.props.book.bookInfo.short_description}  name="short_description"/>

                            <label>Authors:</label>
                            <ul>
                                {
                                    this.props.book.authors.map(author =>
                                        <li key={"author" + author}>{author}</li>
                                    )
                                }
                            </ul>

                            <label>Genres:</label>
                            <ul>
                                {
                                    this.props.book.genres.map(genre =>
                                        <li key={"genre" + genre} > {genre} </li>
                                    )
                                }
                            </ul>

                            <button onClick={this.show} className={'submit-button'}>
                                Order
                            </button>
                        </div>
                    </div>
                );

            } else {
                return <BookOrderForm />
            }
        }
        return content;

    }

}

const mapStateToProps = (state) => {
    return {
        sendingFetch: state.sendingViewFetchRequest,
        failedFetch: state.requestViewFetchFailed,
        book: state.viewBook,
        showForm: state.viewFormShow,
        formRequestSuccess: state.viewFormSuccess
    };
};

export default connect(mapStateToProps)(BookView);
import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import {connect} from 'react-redux';
import {
    bookDeleteRequestFailed,
    bookDeleteRequestFinished,
    sendingDeleteBookRequest,
    bookDeleteReset,
    sendingEditFetchBookRequest,
    bookEditFetchRequestFinished,
    bookEditFetchRequestFailed, currentPageSelected
} from '../actions/BookActions'

class BookItem extends Component {
    constructor(props) {
        super(props);
        this.edit = this.editBook.bind(this);
        this.delete = this.deleteBook.bind(this);
    }

    fetchBook() {
        this.props.dispatch(sendingEditFetchBookRequest());
        fetch(`${address}/home/books/edit/${this.props.book.id}`, {
            method: 'GET',
            headers: {
                'X-REQUESTED-WITH': 'XMLHttpRequest'
            },
        })
            .then(response => response.json())
            .then(
                response => {
                    if (response.success) {
                        this.props.dispatch(bookEditFetchRequestFinished(response.book, response.authors, response.genres));
                    }
                }
            )
            .catch(() => this.props.dispatch(bookEditFetchRequestFailed()));
    }

    sendDeleteRequest() {
        fetch(`${address}/home/delete/${this.props.book.id}`, {
            method: 'POST',
            headers: {
                'X-REQUESTED-WITH': 'XMLHttpRequest'
            },
        })
            .then(response => response.json())
            .then(
                response => {
                    if (response.success) {
                        let currentPage = this.props.currentPage;
                        if(this.props.pageCount - 1 >= 1){
                            if( Math.ceil((this.props.pageCount - 1) / this.props.perPage) < currentPage + 1){
                                currentPage = currentPage - 1;
                                this.props.dispatch(currentPageSelected(currentPage));
                            }
                        }
                        this.props.reload(currentPage)
                        .then(() => this.props.dispatch(bookDeleteRequestFinished()))
                        .then(() => setTimeout(() => this.props.dispatch(bookDeleteReset()), 1000));

                    } else {
                        this.props.dispatch(bookDeleteRequestFailed());
                    }
                }
            )
            .catch(() => this.props.dispatch(bookDeleteRequestFailed()));
    }

    editBook() {
        this.fetchBook();
    }

    deleteBook() {
        let confirmation = confirm(`Are you sure you want to delete book ${this.props.book.name}?`);
        if (confirmation) {
            this.sendDeleteRequest(this.props.book.id);
        }
    }

    render() {
        return (
            <div className="row book-item">
                <div className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                    <span> {this.props.book.name} </span>
                </div>

                <div className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                    <span>{this.props.book.price}</span>
                </div>

                <div className="col-lg-4 col-md-4 col-sm-4 book-table-cell">
                    <span className="book-description">{this.props.book.short_description} </span>
                </div>

                <Link onClick={this.edit} className="col-lg-2 col-md-2 col-sm-2 book-table-cell"
                      to={`/book/edit/${this.props.book.id}`}>
                    <button type="button">
                        Edit
                    </button>
                </Link>

                <div onClick={this.delete} className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                    <button type="button">
                        Delete
                    </button>
                </div>
            </div>
        )
    }
}

const mapStateToProps = (state) => {
    return {
        pageCount: state.pageCount,
        perPage: state.perPage,
        currentPage: state.currentPage
    };
};

export default connect(mapStateToProps)(BookItem);
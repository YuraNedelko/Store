import React, {Component, Fragment} from 'react';
import {connect} from 'react-redux';
import BookItem from "./BookItem.js";
import {Link} from "react-router-dom";
import {
    bookCreateFetchRequestFailed,
    bookCreateFetchRequestFinished,
    bookFetchRequestFailed,
    bookFetchRequestFinished,
    sendingCreateFetchBookRequest,
    sendingFetchBookRequest,
    bookDeleteReset,
    currentPageSelected
} from "../actions/BookActions";
import ReactPaginate from 'react-paginate';


class BookList extends Component {
    constructor(props) {
        super(props);
        this.fetch = this.fetchBooks.bind(this);
        this.handleClick = this.handlePageClick.bind(this);
    }

    handlePageClick(data){
        let selectedPage = data.selected;
        this.props.dispatch(currentPageSelected(selectedPage));
        this.fetchBooks(selectedPage);
    }

    fetchBooks(selectedPage = null) {
        this.props.dispatch(sendingFetchBookRequest());
        return fetch(`${address}/home/books/all/${selectedPage !== null ? selectedPage : (typeof this.props.currentPage 
            === 'undefined' ? 0 : this.props.currentPage)}`, {
            method: 'GET',
            headers: {
                'X-REQUESTED-WITH': 'XMLHttpRequest'
            },
        })
            .then(response => response.json())
            .then(
                response => {
                    if (response.success) {
                        this.props.dispatch(bookFetchRequestFinished(response.books, response.pageCount, response.perPage));
                    }
                }
            )
            .catch(() => this.props.dispatch(bookFetchRequestFailed()));
    }

    fetchCreateProps() {
        this.props.dispatch(sendingCreateFetchBookRequest());
        return fetch(`${address}/home/books/create`, {
            method: 'GET',
            headers: {
                'X-REQUESTED-WITH': 'XMLHttpRequest'
            },
        })
            .then(response => response.json())
            .then(
                response => {
                    if (response.success) {
                        this.props.dispatch(bookCreateFetchRequestFinished(response.authors, response.genres));
                        return true;
                    }
                }
            )
            .catch(() => this.props.dispatch(bookCreateFetchRequestFailed()));
    }

    componentDidMount() {
        //this.props.dispatch(currentPageReset());
        this.props.dispatch(bookDeleteReset());
        this.fetchBooks();
    }

    render() {
        let deleteRequestState = '';
        if (this.props.sendingDelete) {
            deleteRequestState = (
                <div className="alert alert-primary" role="alert">
                    Deleting item ...
                </div>
            );
        } else if (this.props.successDelete) {
            deleteRequestState = (
                <div className="alert alert-success" role="alert">
                    Item was successfully deleted!
                </div>
            );
        } else if (this.props.failedDelete) {
            deleteRequestState = (
                <div className="alert alert-danger" role="alert">
                    Error occurred while deleting item!
                </div>
            );
        }

        let content = '';
        if (this.props.sending) {
            content = (
                <div>
                    Loading ...
                </div>
            );
        } else if (this.props.success) {
            content = (
                <Fragment>
                    <div className="container-fluid">
                        <div className="offset-lg-2 offset-md-2 offset-sm-2 col-lg-8 col-md-8 col-sm-8">
                            <div className="book-list">
                                <div className="row book-item">
                                    <div className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                                        <span>Name</span>
                                    </div>

                                    <div className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                                        <span>Price</span>
                                    </div>

                                    <div className="col-lg-4 col-md-4 col-sm-4 book-table-cell">
                                        <span>Description</span>
                                    </div>

                                    <div className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                                        <span>Edit action</span>
                                    </div>
                                    <div className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                                        <span>Delete action</span>
                                    </div>
                                </div>
                                {this.props.books ?
                                    this.props.books.map(item =>
                                        <BookItem key={item.id} book={item} reload={this.fetch} />
                                    )
                                    : <div>Error occurred</div>
                                }
                            </div>
                        </div>

                        <Link onClick={() => this.fetchCreateProps()}
                              className="offset-lg-4 offset-md-4 offset-sm-4 col-lg-4 col-md-4 col-sm-4 create-button"
                              to={`/book/create/`}>
                            <button type="button">
                                Add book
                            </button>
                        </Link>
                    </div>
                    {
                        this.props.pageCount > 0 ?
                            <ReactPaginate
                                previousLabel={'previous'}
                                nextLabel={'next'}
                                breakClassName="page-item"
                                breakLabel={<a className="page-link">...</a>}
                                pageClassName="page-item"
                                previousClassName="page-item"
                                nextClassName="page-item"
                                pageLinkClassName="page-link"
                                previousLinkClassName="page-link"
                                nextLinkClassName="page-link"
                                pageCount={Math.ceil(this.props.pageCount / this.props.perPage)}
                                forcePage={this.props.currentPage}
                                marginPagesDisplayed={2}
                                onPageChange={this.handleClick}
                                containerClassName={'pagination book_pagination'}
                                subContainerClassName={'pages pagination'}
                                activeClassName={'active'}
                            />
                        :''
                    }
                </Fragment>
            );
        } else if (this.props.failed) {
            content = (
                <div>
                    Error occurred
                </div>
            );
        }
        return (
            <Fragment>
                {deleteRequestState}
                {content}
            </Fragment>
        );
    }
}


const mapStateToProps = (state) => {
    return {
        books: state.books,
        sendingDelete: state.sendingDeleteBookRequest,
        failedDelete: state.requestDeleteFailed,
        successDelete: state.requestDeleteSuccess,
        sending: state.sendingFetchBookRequest,
        failed: state.requestFetchFailed,
        success: state.requestFetchSuccess,
        pageCount: state.pageCount,
        perPage: state.perPage,
        currentPage: state.currentPage
    };
};

export default connect(mapStateToProps)(BookList);


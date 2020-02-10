import React, {Component, Fragment} from 'react';
import {connect} from 'react-redux';
import BookItem from "./BookItem.js";
import {Link} from "react-router-dom";
import {
    bookFetchRequestFailed,
    bookFetchRequestFinished,
    sendingFetchBookRequest,
    currentPageSelected,
    selectedAuthorChanged,
    selectedGenreChanged
} from "../actions/BookActions";
import ReactPaginate from 'react-paginate';

class BookList extends Component {
    constructor(props) {
        super(props);
        this.fetch = this.fetchBooks.bind(this);
        this.handleClick = this.handlePageClick.bind(this);
        this.authorChanged = this.handleAuthorChange.bind(this);
        this.genreChanged = this.handleGenreChange.bind(this);
    }

    handlePageClick(data){
        let selectedPage = data.selected;
        this.props.dispatch(currentPageSelected(selectedPage));
        this.fetchBooks(selectedPage);
    }

    fetchBooks(selectedPage = null, selectedAuthor = null, selectedGenre = null) {
        this.props.dispatch(sendingFetchBookRequest());
        let pageSelected = 0;
        let queryString = '';

        if(selectedAuthor !== null){
            queryString = `?author=${selectedAuthor}` + (this.props.selectedGenre ? `&genre=${this.props.selectedGenre}` : '');
        }else if(selectedGenre !== null) {
            queryString = '?' + (this.props.selectedAuthor ? `author=${this.props.selectedAuthor}&genre=${selectedGenre}` : `genre=${selectedGenre}`);
        }else{
            pageSelected = selectedPage !== null ? selectedPage : (typeof this.props.currentPage
            === 'undefined' ? 0 : this.props.currentPage);
            if(this.props.selectedAuthor && this.props.selectedGenre){
                queryString = `?author=${this.props.selectedAuthor}&genre=${this.props.selectedGenre}`
            } else if(this.props.selectedAuthor && !this.props.selectedGenre) {
                queryString = `?author=${this.props.selectedAuthor}`
            } else if(!this.props.selectedAuthor && this.props.selectedGenre) {
                queryString = `?genre=${this.props.selectedGenre}`
            }
        }

        fetch(`${address}/home/books/all/${pageSelected}${queryString}`, {
            method: 'GET',
            headers: {
                'X-REQUESTED-WITH': 'XMLHttpRequest'
            },
        })
            .then(response => response.json())
            .then(
                response => {
                    if (response.success) {
                        this.props.dispatch(bookFetchRequestFinished(response.books, response.pageCount,
                            response.perPage, response.authors, response.genres));
                    }
                }
            )
            .catch(() => this.props.dispatch(bookFetchRequestFailed()));
    }

    handleAuthorChange(e){
        this.props.dispatch(selectedAuthorChanged(e.target.value));
        this.fetchBooks(null,  e.target.value);
    }

    handleGenreChange(e){
        this.props.dispatch(selectedGenreChanged(e.target.value));
        this.fetchBooks(null, null, e.target.value);
    }

    componentDidMount() {
        this.fetchBooks();
    }

    render() {
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
                    <div className='container-fluid'>
                        <div className='select-container offset-lg-2 offset-md-2 offset-sm-2 col-lg-8 col-md-8 col-sm-8'>
                            <div className='select-block'>
                                <span>Author</span>
                                <select defaultValue={this.props.selectedAuthor} onChange={this.authorChanged}>
                                    <option></option>
                                    {
                                        this.props.authors.map((author) =>
                                            <option key={`author${author.id}`} value={author.id}>{author.name}</option>
                                        )
                                    }
                                </select>
                            </div>

                            <div className='select-block'>
                                <span>Genre</span>
                                <select defaultValue={this.props.selectedGenre} onChange={this.genreChanged}>
                                    <option></option>
                                    {
                                        this.props.genres.map((genre) =>
                                            <option key={`genre${genre.id}`} value={genre.id}>{genre.name}</option>
                                        )
                                    }
                                </select>
                            </div>
                        </div>
                    </div>

                    <div className="container-fluid">
                        <div className="offset-lg-2 offset-md-2 offset-sm-2 col-lg-8 col-md-8 col-sm-8">
                            <div className="book-list">
                                <div className="row book-item">
                                    <div className="col-lg-4 col-md-4 col-sm-4 book-table-cell">
                                        <span>Name</span>
                                    </div>

                                    <div className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                                        <span>Price</span>
                                    </div>

                                    <div className="col-lg-4 col-md-4 col-sm-4 book-table-cell">
                                        <span>Description</span>
                                    </div>

                                    <div className="col-lg-2 col-md-2 col-sm-2 book-table-cell">
                                        <span>View</span>
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
                {content}
            </Fragment>
        );
    }
}


const mapStateToProps = (state) => {
    return {
        books: state.books,
        sending: state.sendingFetchBookRequest,
        failed: state.requestFetchFailed,
        success: state.requestFetchSuccess,
        pageCount: state.pageCount,
        perPage: state.perPage,
        currentPage: state.currentPage,
        authors: state.authors,
        genres: state.genres,
        selectedAuthor: state.selectedAuthor,
        selectedGenre: state.selectedGenre
    };
};

export default connect(mapStateToProps)(BookList);


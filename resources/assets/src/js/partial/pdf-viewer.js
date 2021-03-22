/* global PdfjsWorkerSrc */
'use strict'

import * as pdfjs from 'pdfjs-dist'
import PdfjsWorker from 'pdfjs-dist/build/pdf.worker.js'
import Observer from '@pollen-solutions/support/resources/assets/src/js/mutation-observer'

if (PdfjsWorkerSrc !== undefined) {
  pdfjs.GlobalWorkerOptions.workerSrc = PdfjsWorkerSrc
} else if (typeof window !== "undefined" && "Worker" in window) {
  pdfjs.GlobalWorkerOptions.workerPort = new PdfjsWorker()
}

class PdfViewer {
  constructor(el, options = {}) {
    this.initialized = false
    this.verbose = false

    this.options = {
      classes: {
        content: {
          wrapper: 'PdfViewer-content',
          header: 'PdfViewer-contentHeader',
          body: 'PdfViewer-contentBody',
          footer: 'PdfViewer-contentFooter'
        },
        canvas: 'PdfViewer-canvas',
        nav: {
          wrapper: 'PdfViewer-nav',
          first: 'PdfViewer-navFirst',
          prev: 'PdfViewer-navPrev',
          next: 'PdfViewer-navNext',
          last: 'PdfViewer-navLast'
        },
        page: {
          wrapper: 'PdfViewer-page',
          current: 'PdfViewer-pageCurrent',
          total: 'PdfViewer-pageTotal'
        },
        spinner: 'PdfViewer-spinner'
      },
      nav: {
        first: true,
        prev: true,
        next: true,
        last: true,
      },
      page: {
        current: true,
        total: true
      }
    }

    this.control = {
      content: {
        wrapper: 'content',
        header: 'content.header',
        body: 'content.body',
        footer: 'content.footer'
      },
      canvas: 'canvas',
      nav: {
        wrapper: 'nav',
        first: 'nav.first',
        prev: 'nav.prev',
        next: 'nav.next',
        last: 'nav.last'
      },
      page: {
        wrapper: 'page',
        current: 'page.current',
        total: 'page.total'
      },
      spinner: 'spinner'
    }

    this.el = el

    this.flags = {
      hasContentHeader: false,
      hasContentFooter: false,
      hasNavFirst: true,
      hasNavPrev: true,
      hasNavNext: true,
      hasNavLast: true,
      hasNavStatus: true,
      hasSpinner: true
    }

    this.pdfDoc = undefined
    this.pageNum = 1
    this.pageRendering = false
    this.pageNumPending = null
    this.pageTotal = 0
    this.scale = 1.0
    this.cssUnits = 96 / 72
    this.minWidth = 720

    this.nav = {}
    this.pageInfos = {}

    this._initOptions(options)
    this.initFlags()
    this._initControls()
    this._initEvents()
    this._initDocument()

    this._init()
  }

  // PLUGINS
  // -------------------------------------------------------------------------------------------------------------------
  // Initialisation des options
  _initOptions(options) {
    let tagOptions = this.el.dataset.options || {}

    if (tagOptions) {
      try {
        tagOptions = decodeURIComponent(tagOptions)
      } catch (e) {
        console.log(e)
      }
    }

    try {
      tagOptions = JSON.parse(tagOptions)
    } catch (e) {
      console.log(e)
    }

    if (typeof tagOptions === 'object' && tagOptions !== null) {
      Object.assign(this.options, tagOptions)
    }

    Object.assign(this.options, options)
  }

  // Resolution d'objet depuis une clé à point
  _objResolver(dotKey, obj) {
    return dotKey.split('.').reduce(function (prev, curr) {
      return prev ? prev[curr] : null
    }, obj || self)
  }

  // Initialisation
  _init() {
    this.initialized = true

    if (this.verbose) console.log('PdfViewer fully initialized')
  }

  // Initialisation
  _destroy() {
    this.initialized = true
  }

  // INITIALISATIONS
  // -------------------------------------------------------------------------------------------------------------------
  // Initialisations des drapeaux
  initFlags() {
    this.flags.hasContentHeader = !!this.option('content.header')
    this.flags.hasContentFooter = !!this.option('content.footer')
    this.flags.hasNavFirst = !!this.option('nav.first')
    this.flags.hasNavPrev = !!this.option('nav.prev')
    this.flags.hasNavNext = !!this.option('nav.next')
    this.flags.hasNavLast = !!this.option('nav.last')
    this.flags.hasNav = this.flags.hasNavFirst || this.flags.hasNavPrev || this.flags.hasNavNext || this.flags.hasNavLast
    this.flags.hasPageCurrent = !!this.option('page.current')
    this.flags.hasPageTotal = !!this.option('page.total')
    this.flags.hasPage = this.flags.hasPageCurrent || this.flags.hasPageTotal
    this.flags.hasSpinner = !!this.option('spinner')
    this.flags.isDefered = !!this.option('defer')

    if (this.verbose) console.log('PdfViewer flags initialized')
  }

  // Initialisation des éléments de contrôle.
  _initControls() {
    // -- Content
    let $contentWrapper = this.el.querySelector('[data-pdf-viewer="' + this.control.content.wrapper + '"]')
    if (!$contentWrapper) {
      $contentWrapper = document.createElement('div')
      $contentWrapper.setAttribute('data-pdf-viewer', this.control.content.wrapper)
      this.el.appendChild($contentWrapper)
    }
    $contentWrapper.classList.add(this.option('classes.content.wrapper'))

    // --- Content / Header
    if (this.flags.hasContentHeader) {
      let $contentHeader = $contentWrapper.querySelector('[data-pdf-viewer="' + this.control.content.header + '"]')
      if (!$contentHeader) {
        $contentHeader = document.createElement('header')
        $contentHeader.setAttribute('data-pdf-viewer', this.control.content.header)
        $contentWrapper.appendChild($contentHeader)
      }
      $contentHeader.classList.add(this.option('classes.content.header'))
    }

    // --- Content / Body
    let $contentBody = $contentWrapper.querySelector('[data-pdf-viewer="' + this.control.content.body + '"]')
    if (!$contentBody) {
      $contentBody = document.createElement('main')
      $contentBody.setAttribute('data-pdf-viewer', this.control.content.body)
      $contentWrapper.appendChild($contentBody)
    }
    $contentBody.classList.add(this.option('classes.content.body'))

    // --- Content / Footer
    if (this.flags.hasContentFooter) {
      let $contentFooter = $contentWrapper.querySelector('[data-pdf-viewer="' + this.control.content.footer + '"]')
      if (!$contentFooter) {
        $contentFooter = document.createElement('footer')
        $contentFooter.setAttribute('data-pdf-viewer', this.control.content.footer)
        $contentWrapper.appendChild($contentFooter)
      }
      $contentFooter.classList.add(this.option('classes.content.footer'))
    }

    // -- Canvas
    this.canvas = $contentBody.querySelector('[data-pdf-viewer="' + this.control.canvas + '"]')
    if (!this.canvas) {
      this.canvas = document.createElement('canvas')
      this.canvas.setAttribute('data-pdf-viewer', this.control.canvas)
      $contentBody.appendChild(this.canvas)
    }
    this.canvas.classList.add(this.option('classes.canvas'))

    // -- Nav
    if (this.flags.hasNav) {
      this.nav.wrapper = this.el.querySelector('[data-pdf-viewer="' + this.control.nav.wrapper + '"]')
      if (!this.nav.wrapper) {
        this.nav.wrapper = document.createElement('div')
        this.nav.wrapper.setAttribute('data-pdf-viewer', this.control.nav.wrapper)
        this.el.appendChild(this.nav.wrapper)
      }
      this.nav.wrapper.classList.add(this.option('classes.nav.wrapper'))

      // --- Nav / First
      if (this.flags.hasNavFirst) {
        this.nav.first = this.nav.wrapper.querySelector('[data-pdf-viewer="' + this.control.nav.first + '"]')
        if (!this.nav.first) {
          this.nav.first = document.createElement('button')
          this.nav.first.setAttribute('type', 'button')
          this.nav.first.setAttribute('data-pdf-viewer', this.control.nav.first)
          this.nav.first.innerHTML = '&#171;'
          this.nav.wrapper.appendChild(this.nav.first)
        }
        this.nav.first.classList.add(this.option('classes.nav.first'))
      }

      // --- Nav / Prev
      if (this.flags.hasNavPrev) {
        this.nav.prev = this.nav.wrapper.querySelector('[data-pdf-viewer="' + this.control.nav.prev + '"]')
        if (!this.nav.prev) {
          this.nav.prev = document.createElement('button')
          this.nav.prev.setAttribute('type', 'button')
          this.nav.prev.setAttribute('data-pdf-viewer', this.control.nav.prev)
          this.nav.prev.innerHTML = '&#8249;'
          this.nav.wrapper.appendChild(this.nav.prev)
        }
        this.nav.prev.classList.add(this.option('classes.nav.prev'))
      }

      // --- Nav / Next
      if (this.flags.hasNavNext) {
        this.nav.next = this.nav.wrapper.querySelector('[data-pdf-viewer="' + this.control.nav.next + '"]')
        if (!this.nav.next) {
          this.nav.next = document.createElement('button')
          this.nav.next.setAttribute('type', 'button')
          this.nav.next.setAttribute('data-pdf-viewer', this.control.nav.next)
          this.nav.next.innerHTML = '&#8250;'
          this.nav.wrapper.appendChild(this.nav.next)
        }
        this.nav.next.classList.add(this.option('classes.nav.next'))
      }

      // --- Nav / Last
      if (this.flags.hasNavLast) {
        this.nav.last = this.nav.wrapper.querySelector('[data-pdf-viewer="' + this.control.nav.last + '"]')
        if (!this.nav.last) {
          this.nav.last = document.createElement('button')
          this.nav.last.setAttribute('type', 'button')
          this.nav.last.setAttribute('data-pdf-viewer', this.control.nav.last)
          this.nav.last.innerHTML = '&#187;'
          this.nav.wrapper.appendChild(this.nav.last)
        }
        this.nav.last.classList.add(this.option('classes.nav.last'))
      }
    }

    // -- Page Infos
    if (this.flags.hasPage) {
      let $pageInfos = this.el.querySelector('[data-pdf-viewer="' + this.control.page.wrapper + '"]')
      if (!$pageInfos ) {
        $pageInfos = document.createElement('div')
        $pageInfos.setAttribute('data-pdf-viewer', this.control.page.wrapper)
        this.el.appendChild($pageInfos)
      }
      $pageInfos.classList.add(this.option('classes.page.wrapper'))

      // --- Page Infos / Current
      if (this.flags.hasPageCurrent) {
        this.pageInfos.current = $pageInfos.querySelector('[data-pdf-viewer="' + this.control.page.current + '"]')
        if (!this.pageInfos.current) {
          this.pageInfos.current = document.createElement('span')
          this.pageInfos.current.setAttribute('data-pdf-viewer', this.control.page.current)
          $pageInfos.appendChild(this.pageInfos.current)
        }
        this.pageInfos.current.classList.add(this.option('classes.page.current'))
      }

      // --- Page Infos / Total
      if (this.flags.hasPageTotal) {
        this.pageInfos.total = $pageInfos.querySelector('[data-pdf-viewer="' + this.control.page.total + '"]')
        if (!this.pageInfos.total) {
          this.pageInfos.total = document.createElement('span')
          this.pageInfos.total.setAttribute('data-pdf-viewer', this.control.page.total)
          $pageInfos.appendChild(this.pageInfos.total)
        }
        this.pageInfos.total.classList.add(this.option('classes.page.total'))
      }
    }

    // -- Spinner
    if (this.flags.hasSpinner) {
      this.spinner = this.el.querySelector('[data-pdf-viewer="' + this.control.spinner + '"]')
      if (!this.spinner) {
        this.spinner = document.createElement('div')
        this.spinner.setAttribute('data-pdf-viewer', this.control.spinner)
        this.spinner.innerHTML = "<span>Chargement ...</span>"
        this.el.appendChild(this.spinner)
      }
      this.spinner.classList.add(this.option('classes.spinner'))
      this._doSpinnerDisplay('hide')
    }

    if (this.verbose) console.log('PdfViewer controls initialized')
  }

  // Initialisation des événements déclenchement.
  _initEvents() {
    let self = this

    if (this.flags.hasNav) {
      if (this.flags.hasNavFirst) {
        this.nav.first.addEventListener('click', async (e) => {
          await self._onClickNavFirst(e, self)
        })
      }
      if (this.flags.hasNavPrev) {
        this.nav.prev.addEventListener('click', async (e) => {
          await self._onClickNavPrev(e, self)
        })
      }
      if (this.flags.hasNavNext) {
        this.nav.next.addEventListener('click', async (e) => {
          await self._onClickNavNext(e, self)
        })
      }
      if (this.flags.hasNavLast) {
        this.nav.last.addEventListener('click', async (e) => {
          await self._onClickNavLast(e, self)
        })
      }
    }
    if (this.verbose) console.log('PdfViewer events initialized')
  }

  // Initialisation du document PDF.
  _initDocument() {
    let self = this

    if (!this.flags.isDefered) {
      this._doGetPdfDoc()
          .then(() => {
            return self._doGetPdfDocPage(self.pageNum)
          })
          .then(pdfPage => {
            return self._doPdfDocPageRender(pdfPage)
          })
          .catch(error => {
            console.error(error)
          })
    }
  }

  // EVENEMENTS
  // -----------------------------------------------------------------------------------------------------------------
  // Navigation vers la première page.
  async _onClickNavFirst(e, self) {
    e.preventDefault()

    await self.first()
  }

  // Navigation vers la page précédente.
  async _onClickNavPrev(e, self) {
    e.preventDefault()

    await self.prev()
  }

  // Navigation vers la page suivante.
  async _onClickNavNext(e, self) {
    e.preventDefault()

    await self.next()
  }

  // Navigation vers la dernière page.
  async _onClickNavLast(e, self) {
    e.preventDefault()

    await self.last()
  }

  // ACTIONS
  // -----------------------------------------------------------------------------------------------------------------
  /**
   * Chargement du document PDF.
   *
   * @return {PDFDocumentProxy}
   *
   * @async
   */
  async _doGetPdfDoc() {
    let src = this.option('src') || undefined

    if (src !== undefined) {
      this._doSpinnerDisplay('show')

      try {
        /** @type {PDFDocumentProxy} pdfDoc */
        let pdfDoc = await pdfjs.getDocument(src).promise

        this.pdfDoc = pdfDoc
        this.pageTotal = pdfDoc.numPages

        if (this.verbose) console.log('PdfViewer document loaded')

        return pdfDoc
      } catch (e) {
        console.log(e)
      }
    }
  }

  /**
   * Récupération d'une page du document PDF.
   *
   * @param {number} num
   *
   * @return {PDFPageProxy}
   *
   * @async
   */
  async _doGetPdfDocPage(num) {
    this._doSpinnerDisplay('show')
    this.pageRendering = true

    /** @type {PDFPageProxy} pdfPage */
    let pdfPage = await this.pdfDoc.getPage(num)
    if (this.verbose) console.log('PdfViewer Page [' + num + '] initialized')

    return pdfPage
  }

  /**
   * Récupération d'une page du document PDF.
   *
   * @param {PDFPageProxy} pdfPage
   *
   * @return {RenderTask}
   *
   * @async
   */
  async _doPdfDocPageRender(pdfPage) {
    let pageViewport = pdfPage.getViewport({scale: this.scale}),
        renderContext = this.canvas.getContext('2d')

    if (this.minWidth !== 0) {
      const minWidth = this.el.clientWidth > this.minWidth ? this.el.clientWidth : this.minWidth
      pageViewport = pdfPage.getViewport({scale: minWidth / pageViewport.width})
    }

    this.canvas.width = pageViewport.width || pageViewport.viewBox[2]
    this.canvas.height = pageViewport.height || pageViewport.viewBox[3]

    const renderTask = pdfPage.render({
      canvasContext: renderContext,
      viewport: pageViewport
    })

    await renderTask.promise
    if (this.verbose) console.log('PdfViewer Page rendered')

    this.pageRendering = false
    this._doSpinnerDisplay('hide')

    if (this.pageNumPending !== null) {
      await this._doGetPdfDocPage(this.pageNumPending)
      this.pageNumPending = null
    }

    this._doNavUpdate()

    return renderTask
  }

  /**
   * Mise en file de l'affichage d'une page.
   *
   * @param {number} num
   *
   * @return {undefined}
   *
   * @async
   */
  async _doPageRenderQueue(num) {
    if (this.pageRendering) {
      this.pageNumPending = num
    } else {
      const pdfPage = await this._doGetPdfDocPage(num)
      await this._doPdfDocPageRender(pdfPage)
    }
  }

  // Mise à jour de la pagination.
  _doNavUpdate() {
    if (this.pageTotal <= 1) {
      this.nav.wrapper.setAttribute('aria-hidden', 'true')
    } else {
      this.nav.wrapper.setAttribute('aria-hidden', 'false')
    }

    this.pageInfos.current.innerHTML = this.pageNum
    this.pageInfos.total.innerHTML = this.pageTotal

    if (this.pageNum <= 2) {
      this.nav.first.setAttribute('aria-disabled', 'true')
    } else {
      this.nav.first.setAttribute('aria-disabled', 'false')
    }

    if (this.pageNum <= 1) {
      this.nav.prev.setAttribute('aria-disabled', 'true')
    } else {
      this.nav.prev.setAttribute('aria-disabled', 'false')
    }

    if (this.pageNum >= this.pageTotal) {
      this.nav.next.setAttribute('aria-disabled', 'true')
    } else {
      this.nav.next.setAttribute('aria-disabled', 'false')
    }

    if (this.pageNum + 1 >= this.pageTotal) {
      this.nav.last.setAttribute('aria-disabled', 'true')
    } else {
      this.nav.last.setAttribute('aria-disabled', 'false')
    }
  }

  // Affichage/Masquage de l'indicateur de chargement.
  _doSpinnerDisplay(display = 'toggle') {
    switch (display) {
      default:
        if (this.spinner.getAttribute('aria-hidden') === 'false') {
          this._doSpinnerDisplay('hide')
        } else {
          this._doSpinnerDisplay('show')
        }
        break
      case 'hide' :
        this.spinner.setAttribute('aria-hidden', 'true')
        break
      case 'show' :
        this.spinner.setAttribute('aria-hidden', 'false')
        break
    }
  }

  // ACCESSEURS
  // -------------------------------------------------------------------------------------------------------------------
  // Récupération d'options (syntaxe à point permise)
  option(key = null, defaults = null) {
    if (key === null) {
      return this.options
    }

    return this._objResolver(key, this.options) ?? defaults
  }

  // Chargement de la dernière page.
  async first() {
    if (this.pageNum === 1) {
      return
    }

    await this._doPageRenderQueue(this.pageNum = 1)
  }

  // Chargement de la page précédente.
  async prev() {
    if (this.pageNum <= 1) {
      return
    }

    await this._doPageRenderQueue(--this.pageNum)
  }

  // Chargement de la page suivante.
  async next() {
    if (this.pageNum >= this.pageTotal) {
      return
    }

    await this._doPageRenderQueue(++this.pageNum)
  }

  // Chargement de la dernière page.
  async last() {
    if (this.pageNum === this.pageTotal) {
      return
    }

    await this._doPageRenderQueue(this.pageNum = this.pageTotal)
  }

  // Chargement d'une page.
  async load(page) {
    let self = this

    if (this.pdfDoc === undefined) {
        await this._doGetPdfDoc()
        const pdfPage = await self._doGetPdfDocPage(self.pageNum)
        await self._doPdfDocPageRender(pdfPage)
    } else {
      await this._doPageRenderQueue(page ? page : this.pageNum)
    }
  }
}

window.addEventListener('load', () => {
  const $elements = document.querySelectorAll('[data-observe="pdf-viewer"]')

  if ($elements) {
    for (const $el of $elements) {
      new PdfViewer($el)
    }
  }

  Observer('[data-observe="pdf-viewer"]', function ($el) {
    new PdfViewer($el)
  })
})

export default PdfViewer